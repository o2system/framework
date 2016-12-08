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
use O2System\Framework\Http\Message\Request;
use O2System\Framework\Http\Router\Maps;
use O2System\Framework\Http\Router\Registries\Route;

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
    protected $segments = [ ];

    protected $request;

    protected $controller;

    // ------------------------------------------------------------------------

    final public function parseRequest ( Request $request )
    {
        $this->request = $request;

        $uriSegments = $request->getUri()->getSegments();
        $uriString = $request->getUri()->getString();

        if ( false !== ( $moduleUriSegments = $this->parseModuleSegments( $uriSegments ) ) ) {
            $uriSegments = $moduleUriSegments;
            $uriSegments = array_unique( $uriSegments );

            $uriString = str_replace( modules()->current()->getParameter() . '/', '', $uriString );
        }

        $routes = config( 'routes' );
        $routeDefault = null;

        if ( $routes instanceof Maps ) {
            // Check route by mapped uri string
            if ( null !== ( $route = $routes->getItem( $uriString ) ) ) {
                if ( $route->isValidHttpMethod( $request->getMethod() ) AND $route->isValidUriString( $uriString ) ) {
                    if ( $this->parseRoute( $route ) !== false ) {
                        return;
                    }
                }
            } else {
                // Check route by mapped regex uri string
                foreach ( $routes as $key => $route ) {

                    if ( $key === '/' ) {
                        $routeDefault = $route;
                    }

                    if ( $route->isValidHttpMethod( $request->getMethod() ) AND $route->isValidUriString(
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
        }

        // Automatic routing
        $this->parseSegments( $uriSegments );

        if( empty( $this->controller) and isset( $routeDefault ) ) {
            if ( $this->parseRoute( $routeDefault, $uriSegments ) !== false ) {
                return;
            }
        }

        // Let's the framework do the rest when there is no controller found
        // the framework will redirect to PAGE 404
    }

    // ------------------------------------------------------------------------

    final private function parseModuleSegments ( array $segments )
    {
        $numSegments = count( $segments );

        for ( $i = 0; $i <= $numSegments; $i++ ) {
            $routedSegments = array_slice( $segments, 0, ( $numSegments - $i ) );

            if ( false !== ( $module = modules()->getModule( $routedSegments ) ) ) {
                $uriSegments = array_diff( $segments, $routedSegments );
                $uriSegments = empty( $uriSegments )
                    ? [ $module->getParameter() ]
                    : $uriSegments;

                // Register Module Namespace
                loader()->addNamespace( $module->getNamespace(), $module->getRealPath() );

                // Push Module
                modules()->push( $module );

                return $uriSegments;

                break;
            }
        }

        return false;
    }

    final private function parseRoute ( Route $route, array $uriSegments = [] )
    {
        if ( $closure = $route->getClosure() ) {
            if ( $closure instanceof Controller ) {
                $controllerRegistry = new Router\Registries\Controller( $closure );
                $uriSegments = empty( $uriSegments ) ? $route->getClosureParameters() : $uriSegments;
                $this->setController( $controllerRegistry, $uriSegments );
            } elseif ( $closure instanceof Router\Registries\Controller ) {
                $this->setController( $closure, $route->getClosureParameters() );
            } elseif ( is_array( $closure ) ) {
                $this->parseSegments( $closure );
            } elseif ( is_string( $closure ) ) {
                $this->sendOutput( $closure );
            } elseif ( is_null( $closure ) ) {
                exit( EXIT_SUCCESS );
            }
        }

        return false;
    }

    final private function parseSegments ( array $segments )
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

    final protected function sendOutput ( $output )
    {
        echo $output;
        exit( EXIT_SUCCESS );
    }

    // ------------------------------------------------------------------------

    public function getRequest ()
    {
        return $this->request;
    }

    // ------------------------------------------------------------------------

    /**
     * getController
     *
     * @return Router\Registries\Controller
     */
    final public function getController ()
    {
        return $this->controller;
    }

    // ------------------------------------------------------------------------

    final protected function setController ( Router\Registries\Controller $controller, array $uriSegments = [ ] )
    {
        if ( is_null( $controller->name ) ) {
            output()->showError( 400, 'BAD_REQUEST_HEADER', 'BAD_REQUEST_MESSAGE' );
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
                    output()->showError( 404, 'NOT_FOUND_HEADER', 'NOT_FOUND_MESSAGE' );
                }
            }
        } elseif ( $controller->hasMethod( 'reroute' ) ) {
            $controller
                ->setRequestMethod( 'reroute' )
                ->setRequestMethodArgs( [ 'index', [ ] ] );
        } elseif ( $controller->hasMethod( 'index' ) ) {
            $controller
                ->setRequestMethod( 'index' );
        }

        // Set Router Controller
        $this->controller = $controller;
    }

    // ------------------------------------------------------------------------

    final private function validateSegmentsPage ( array $segments )
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
                    $page = new Router\Registries\Page( $pageFilePath );
                    break;
                } elseif ( is_file( $pageFilePath = $pagesDirectory . $pageFilename ) ) {
                    $page = new Router\Registries\Page( $pageFilePath );
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
                                ( new Router\Registries\Controller( $controller ) )
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

    final private function validateSegmentsController ( array $segments )
    {
        $numSegments = count( $segments );
        $controllerRegistry = null;
        $uriSegments = [ ];
        $controllersDirectories = modules()->getDirs( 'Controllers' );

        for ( $i = 0; $i <= $numSegments; $i++ ) {
            $routedSegments = array_slice( $segments, 0, ( $numSegments - $i ) );

            $controllerFilename = implode( DIRECTORY_SEPARATOR, $routedSegments );
            $controllerFilename = prepare_filename( $controllerFilename ) . '.php';

            if ( empty( $controllerRegistry ) ) {
                foreach ( $controllersDirectories as $controllerDirectory ) {
                    if ( is_file( $controllerFilePath = $controllerDirectory . $controllerFilename ) ) {
                        $uriSegments = array_diff( $segments, $routedSegments );
                        $controllerRegistry = new Router\Registries\Controller( $controllerFilePath );

                        break;
                    }
                }
            } elseif ( $controllerRegistry instanceof Router\Registries\Controller ) {
                $this->setController( $controllerRegistry, $uriSegments );

                return true;

                break;
            }
        }

        return false;
    }
}