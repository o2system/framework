<?php
/**
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */
// ------------------------------------------------------------------------

namespace O2System\Framework\Http;

// ------------------------------------------------------------------------

use O2System\Framework\Http\Router\Datastructures\Route;

/**
 * Class Router
 *
 * @package O2System
 */
class Router
{
    /**
     * Router segments
     *
     * @var array
     */
    protected $segments = [];

    protected $request;

    protected $controller;

    // ------------------------------------------------------------------------

    final public function parseRequest()
    {
        $uri = request()->getUri();

        // Load default routes config
        $routes = config()->loadFile( 'routes', true );

        $this->segments = $uriSegments = $uri->getSegments()->getParts();
        $uriString = $uri->getSegments()->getString();

        // Domain routing
        if ( null !== ( $domain = $routes->getDomain() ) ) {
            if ( is_array( $domain ) ) {
                $this->segments = $uriSegments = array_merge( $domain, $uriSegments );
                $uriString = implode( '/', array_map( 'dash', $uriSegments ) );
            } else {
                if ( false !== ( $domainAppModule = modules()->getApp( $domain ) ) ) {
                    // Register Domain App Module Namespace
                    loader()->addNamespace( $domainAppModule->getNamespace(), $domainAppModule->getRealPath() );

                    // Push Domain App Module
                    modules()->push( $domainAppModule );
                } elseif ( false !== ( $module = modules()->getModule( $domain ) ) ) {
                    // Register Path Module Namespace
                    loader()->addNamespace( $module->getNamespace(), $module->getRealPath() );

                    // Push Path Module
                    modules()->push( $module );
                }
            }
        } elseif ( false !== ( $subdomain = $uri->getSubdomain() ) ) {
            if ( false !== ( $subdomainAppModule = modules()->getApp( $subdomain ) ) ) {
                // Register Subdomain App Module Namespace
                loader()->addNamespace( $subdomainAppModule->getNamespace(), $subdomainAppModule->getRealPath() );

                // Push Subdomain App Module
                modules()->push( $subdomainAppModule );
            }
        }

        // Path to Module automatic routing
        if ( $uriTotalSegments = count( $uriSegments ) ) {
            for ( $i = 0; $i <= $uriTotalSegments; $i++ ) {
                $uriRoutedSegments = array_slice( $uriSegments, 0, ( $uriTotalSegments - $i ) );

                if ( false !== ( $module = modules()->getModule( $uriRoutedSegments ) ) ) {
                    $uriSegments = array_diff( $uriSegments, $uriRoutedSegments );
                    $this->segments = $uriSegments = empty( $uriSegments )
                        ? [ $module->getParameter() ]
                        : $uriSegments;

                    // Register Path Module Namespace
                    loader()->addNamespace( $module->getNamespace(), $module->getRealPath() );

                    // Push Path Module
                    modules()->push( $module );

                    break;
                }
            }
        }

        // Load routes config
        $configDir = modules()->current()->getDir( 'config', true );
        if ( is_file(
            $filePath = $configDir . ucfirst(
                    strtolower( ENVIRONMENT )
                ) . DIRECTORY_SEPARATOR . 'Routes.php'
        ) ) {
            unset( $routes );
            include( $filePath );
        } elseif ( is_file(
            $filePath = $configDir . 'Routes.php'
        ) ) {
            unset( $routes );
            include( $filePath );
        } elseif ( $module = modules()->current() ) {
            $controllerClassName = $module->getNamespace() . 'Controllers\\' . studlycase( $module->getParameter() );
            if ( class_exists( $controllerClassName ) ) {
                $routeModuleDefault = $routes->any( '/', function () use ( $controllerClassName ) {
                    return new $controllerClassName();
                } )->getMap( '/' );
            }
        }

        if ( ! isset( $routes ) or ! $routes instanceof Router\Routes ) {
            // @todo: throw config route exception
            return;
        }

        // Define default route
        $routeMapDefault = $routes->getMap( '/' );
        if ( $routeMapDefault === false ) {
            if ( isset( $routeModuleDefault ) ) {
                $routeDefault = $routeModuleDefault;
            } else {
                $controllerClassName = modules()->current()->getNamespace() . 'Controllers\\' . studlycase( modules()->current()->getParameter() );
                if ( class_exists( $controllerClassName ) ) {
                    $routeDefault = $routes->any( '/', function () use ( $controllerClassName ) {
                        return new $controllerClassName();
                    } )->getMap( '/' );
                }
            }
        } else {
            $routeDefault = $routeMapDefault;
        }

        if ( empty( $uriString ) and empty( $this->controller ) and isset( $routeDefault ) ) {
            if ( $this->parseRoute( $routeDefault, $uriSegments ) !== false ) {
                return;
            }
        } elseif ( false !== ( $route = $routes->getMap( $uriString ) ) ) {
            if ( $route->isValidUriString( $uriString ) ) {
                if ( ! $route->isValidHttpMethod( request()->getMethod() ) and
                    ! $route->isAnyHttpMethod()
                ) {
                    output()->sendError( 405 );
                } elseif ( $this->parseRoute( $route ) !== false ) {
                    return;
                }
            }
        } elseif ( count( $maps = $routes->getMaps() ) ) {
            foreach ( $maps as $key => $route ) {
                if ( $route->isValidHttpMethod( request()->getMethod() ) and $route->isValidUriString(
                        $uriString
                    )
                ) {
                    if ( $this->parseRoute( $route ) !== false ) {
                        return;

                        break;
                    }
                }
            }
        }

        // Try to parse the uri segments to perform automatic routing
        $this->parseSegments( $uriSegments );

        // Let's the framework do the rest when there is no controller found
        // the framework will redirect to PAGE 404
    }

    // ------------------------------------------------------------------------

    final private function parseRoute( Route $route, array $uriSegments = [] )
    {
        if ( $closure = $route->getClosure() ) {
            if ( $closure instanceof Controller ) {
                $controllerRegistry = new Router\Datastructures\Controller( $closure );
                $uriSegments = empty( $uriSegments )
                    ? $route->getClosureParameters()
                    : $uriSegments;
                $this->setController( $controllerRegistry, $uriSegments );
            } elseif ( $closure instanceof Router\Datastructures\Controller ) {
                $this->setController( $closure, $route->getClosureParameters() );
            } elseif ( is_array( $closure ) ) {
                $this->parseSegments( $closure );
            } elseif ( is_string( $closure ) ) {
                if ( strpos( $closure, '/' ) !== false ) {
                    $this->parseSegments( explode( '/', $closure ) );
                } else {
                    output()->send( $closure );
                }
            }
        } else {
            output()->sendError( 204 );
        }
    }

    final private function parseSegments( array $segments )
    {
        static $reflection;

        if ( empty( $reflection ) ) {
            $reflection = new \ReflectionClass( $this );
        }

        foreach ( $reflection->getMethods() as $method ) {
            if ( strpos( $method->name, 'validateSegments' ) !== false ) {
                if ( $this->{$method->name}( $segments ) ) {
                    break;
                }
            }
        }
    }

    /**
     * getController
     *
     * @return Router\Datastructures\Controller
     */
    final public function getController()
    {
        return $this->controller;
    }

    // ------------------------------------------------------------------------

    final protected function setController( Router\Datastructures\Controller $controller, array $uriSegments = [] )
    {
        if ( ! $controller->isValid() ) {
            output()->sendError( 400 );
        }

        // Add Controller PSR4 Namespace
        loader()->addNamespace( $controller->getNamespaceName(), $controller->getFileInfo()->getPath() );

        $controllerMethod = camelcase( reset( $uriSegments ) );
        $controllerMethodParams = array_slice( $uriSegments, 1 );

        if ( null !== $controller->getRequestMethod() ) {
            $controller->setRequestMethodArgs( $controllerMethodParams );
        } elseif ( count( $uriSegments ) ) {
            if ( $controller->hasMethod( 'reroute' ) ) {
                $controller
                    ->setRequestMethod( 'reroute' )
                    ->setRequestMethodArgs(
                        [
                            $controllerMethod,
                            $controllerMethodParams,
                        ]
                    );
            } elseif ( $controller->hasMethod( $controllerMethod ) ) {
                $method = $controller->getMethod( $controllerMethod );

                if ( $method->isPublic() ) {
                    $controller
                        ->setRequestMethod( $controllerMethod )
                        ->setRequestMethodArgs( $controllerMethodParams );
                } elseif ( is_ajax() AND $method->isProtected() ) {
                    $controller
                        ->setRequestMethod( $controllerMethod )
                        ->setRequestMethodArgs( $controllerMethodParams );
                }
            } elseif ( $controller->hasMethod( 'index' ) ) {
                $index = $controller->getMethod( 'index' );

                if ( $index->getNumberOfParameters() > 0 ) {

                    array_unshift( $controllerMethodParams, $controllerMethod );

                    $controller
                        ->setRequestMethod( 'index' )
                        ->setRequestMethodArgs( $controllerMethodParams );
                } else {
                    output()->sendError( 404 );
                }
            }
        } elseif ( $controller->hasMethod( 'reroute' ) ) {
            $controller
                ->setRequestMethod( 'reroute' )
                ->setRequestMethodArgs( [ 'index', [] ] );
        } elseif ( $controller->hasMethod( 'index' ) ) {
            $controller
                ->setRequestMethod( 'index' );
        }

        // Set Router Controller
        $this->controller = $controller;
    }

    // ------------------------------------------------------------------------

    final private function validateSegmentsPage( array $segments )
    {
        $languageDirectory = language()->getDefault();

        if ( language()->isExists( $languageIdeom = reset( $segments ) ) ) {
            $languageDirectory = $languageIdeom;
            array_shift( $segments );
        }

        $numSegments = count( $segments );
        $pagesDirectories = modules()->getDirs( 'Pages' );

        for ( $i = 0; $i <= $numSegments; $i++ ) {
            $routedSegments = array_slice( $segments, 0, ( $numSegments - $i ) );

            $pageFilename = implode( DIRECTORY_SEPARATOR, $routedSegments );
            $pageFilename = strtolower( $pageFilename ) . '.phtml';

            foreach ( $pagesDirectories as $pagesDirectory ) {
                if ( is_file(
                    $pageFilePath = $pagesDirectory . $languageDirectory . DIRECTORY_SEPARATOR . $pageFilename
                ) ) {
                    $page = new Router\Datastructures\Page( $pageFilePath );
                    break;
                } elseif ( is_file( $pageFilePath = $pagesDirectory . $pageFilename ) ) {
                    $page = new Router\Datastructures\Page( $pageFilePath );
                    break;
                }
            }

            if ( isset( $page ) ) {
                foreach ( modules()->getNamespaces() as $controllersNamespace ) {
                    $controllerPagesClassName = $controllersNamespace->name . 'Controllers\Pages';

                    if ( $controllersNamespace->name === 'O2System\Framework\\' ) {
                        $controllerPagesClassName = 'O2System\Framework\Http\Controllers\Pages';
                    }

                    if ( class_exists( $controllerPagesClassName ) ) {
                        $controller = new $controllerPagesClassName();

                        if ( $controller instanceof Controllers\Pages ) {
                            $controller->setPage( $page );

                            $this->setController(
                                ( new Router\Datastructures\Controller( $controller ) )
                                    ->setRequestMethod( 'index' )
                            );
                        }

                        break;
                    }
                }

                return true;

                break;
            }
        }

        return false;
    }

    final private function validateSegmentsController( array $segments )
    {
        $numSegments = count( $segments );
        $controllerRegistry = null;
        $uriSegments = [];
        $controllersDirectories = modules()->getDirs( 'Controllers' );

        for ( $i = 0; $i <= $numSegments; $i++ ) {
            $routedSegments = array_slice( $segments, 0, ( $numSegments - $i ) );

            $controllerFilename = implode( DIRECTORY_SEPARATOR, $routedSegments );
            $controllerFilename = prepare_filename( $controllerFilename ) . '.php';

            if ( empty( $controllerRegistry ) ) {
                foreach ( $controllersDirectories as $controllerDirectory ) {
                    if ( is_file( $controllerFilePath = $controllerDirectory . $controllerFilename ) ) {
                        $uriSegments = array_diff( $segments, $routedSegments );
                        $controllerRegistry = new Router\Datastructures\Controller( $controllerFilePath );
                        break;
                    }
                }
            } elseif ( $controllerRegistry instanceof Router\Datastructures\Controller ) {
                $this->setController( $controllerRegistry, $uriSegments );

                return true;
                break;
            }
        }

        return false;
    }
}