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

use O2System\Framework\Http\Message\Uri;
use O2System\Framework\Http\Router\Datastructures\Page;
use O2System\Framework\Http\Router\Datastructures\Action;

// ------------------------------------------------------------------------

/**
 * Class Router
 *
 * @package O2System
 */
class Router
{
    final public function parseRequest( Uri $uri = null )
    {
        $uri = is_null( $uri ) ? request()->getUri() : $uri;
        $uriSegments = $uri->getSegments()->getParts();
        $uriString = $uri->getSegments()->getString();

        if ( empty( $uriSegments ) ) {
            $uriPath = urldecode(
                parse_url( $_SERVER[ 'REQUEST_URI' ], PHP_URL_PATH )
            );

            if( $uriPath !== '/' ) {
                $uriString = $uriPath;
                $uriSegments = array_filter( explode( '/', $uriString ) );
            }
        }

        // Load app addresses config
        $addresses = config()->loadFile( 'addresses', true );

        if ( $addresses instanceof Router\Addresses ) {
            // Domain routing
            if ( null !== ( $domain = $addresses->getDomain() ) ) {
                if ( is_array( $domain ) ) {
                    $uriSegments = array_merge( $domain, $uriSegments );
                    $uriString = implode( '/', array_map( 'dash', $uriSegments ) );
                } elseif ( is_string( $domain ) ) {
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
                if ( false !== ( $module = modules()->getApp( $subdomain ) ) ) {
                    // Register Subdomain App Module Namespace
                    loader()->addNamespace( $module->getNamespace(), $module->getRealPath() );

                    // Push Subdomain App Module
                    modules()->push( $module );

                    // Load modular addresses config
                    if ( false !== ( $configDir = $module->getDir( 'config', true ) ) ) {

                        if ( is_file(
                            $filePath = $configDir . ucfirst(
                                    strtolower( ENVIRONMENT )
                                ) . DIRECTORY_SEPARATOR . 'Addresses.php'
                        ) ) {
                            include( $filePath );
                        } elseif ( is_file(
                            $filePath = $configDir . 'Addresses.php'
                        ) ) {
                            include( $filePath );
                        }
                    }
                }
            }
        }

        // Define default action by app addresses config
        $defaultAction = $addresses->getTranslation( '/' );

        // Module routing
        if ( $uriTotalSegments = count( $uriSegments ) ) {
            for ( $i = 0; $i <= $uriTotalSegments; $i++ ) {
                $uriRoutedSegments = array_diff( $uriSegments,
                    array_slice( $uriSegments, ( $uriTotalSegments - $i ) ) );

                if ( false !== ( $module = modules()->getModule( $uriRoutedSegments ) ) ) {
                    $uriSegments = array_diff( $uriSegments, $uriRoutedSegments );
                    $uriSegments = empty( $uriSegments )
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

        if ( ! empty( $module ) ) {
            // Load modular addresses config
            if ( false !== ( $configDir = $module->getDir( 'config', true ) ) ) {

                if ( is_file(
                    $filePath = $configDir . ucfirst(
                            strtolower( ENVIRONMENT )
                        ) . DIRECTORY_SEPARATOR . 'Routes.php'
                ) ) {
                    include( $filePath );
                } elseif ( is_file(
                    $filePath = $configDir . 'Routes.php'
                ) ) {
                    include( $filePath );
                }

                if ( isset( $addresses ) && $addresses instanceof Router\Addresses ) {
                    $moduleRouteDefault = $addresses->getTranslation( '/' );
                }
            }

            // Define default action by module addresses config
            if ( empty( $moduleRouteDefault ) ) {
                $controllerClassName = $module->getNamespace() . 'Controllers\\' . studlycase( $module->getParameter() );
                if ( class_exists( $controllerClassName ) ) {
                    $defaultAction = $addresses->any( '/', function () use ( $controllerClassName ) {
                        //return new $controllerClassName();
                        return new Router\Datastructures\Controller( $controllerClassName );
                    } )->getTranslation( '/' );

                    $uriSegments = array_diff( $uriSegments, [ strtolower( $module->getParameter() ) ] );

                    $defaultAction->setClosureParameters( $uriSegments );
                }
            } else {
                $defaultAction = $moduleRouteDefault;
            }
        }

        // Try to get action from URI String
        if ( false !== ( $action = $addresses->getTranslation( $uriString ) ) ) {
            if ( $action->isValidUriString( $uriString ) ) {
                if ( ! $action->isValidHttpMethod( request()->getMethod() ) && ! $action->isAnyHttpMethod() ) {
                    output()->sendError( 405 );
                } elseif ( $this->parseAction( $action ) !== false ) {
                    return;
                }
            }
        }

        // Try to get route from controller & page
        if ( $uriTotalSegments = count( $uriSegments ) ) {
            for ( $i = 0; $i <= $uriTotalSegments; $i++ ) {
                $uriRoutedSegments = array_slice( $uriSegments, 0, ( $uriTotalSegments - $i ) );

                foreach ( modules()->getArrayCopy() as $module ) {

                    $controllerNamespace = $module->getNamespace() . 'Controllers\\';
                    if ( $module->getNamespace() === 'O2System\Framework\\' ) {
                        $controllerNamespace = 'O2System\Framework\Http\Controllers\\';
                    }

                    $controllerClassName = $controllerNamespace . implode( '\\',
                            array_map( 'studlycase', $uriRoutedSegments ) );

                    $classes[] = $controllerClassName;

                    if ( class_exists( $controllerClassName ) ) {
                        $defaultAction = $addresses->any( '/', function () use ( $controllerClassName ) {
                            return new Router\Datastructures\Controller( $controllerClassName );
                        } )->getTranslation( '/' );

                        $uriSegments = array_diff( $uriSegments, $uriRoutedSegments );

                        $defaultAction->setClosureParameters( $uriSegments );

                        if ( $this->parseAction( $defaultAction, $uriSegments ) !== false ) { // Try to parse route
                            return;
                        }

                        break;
                    } elseif ( false !== ( $pagesDir = $module->getDir( 'pages', true ) ) ) {
                        $pageFilePath = $pagesDir . implode( DIRECTORY_SEPARATOR,
                                array_map( 'studlycase', $uriRoutedSegments ) ) . '.phtml';

                        if ( is_file( $pageFilePath ) ) {
                            if ( $this->setPage( new Page( $pageFilePath ) ) !== false ) {
                                return;

                                break;
                            }
                        }
                    }
                }

                // break the loop if the controller has been set
                if ( ! empty( o2system()->hasService( 'controller' ) ) ) {
                    break;
                }
            }
        } elseif ( count( $maps = $addresses->getTranslations() ) ) { // Try to parse route from route map
            foreach ( $maps as $map ) {
                if ( $map instanceof Action ) {
                    if ( $map->isValidHttpMethod( request()->getMethod() ) && $map->isValidUriString( $uriString ) ) {
                        if ( $this->parseAction( $map ) !== false ) {
                            return;

                            break;
                        }
                    }
                }
            }
        }

        // try to get default action
        if ( isset( $defaultAction ) ) {
            $this->parseAction( $defaultAction, $uriSegments );
        }

        // Let's the framework do the rest when there is no controller found
        // the framework will redirect to PAGE 404
    }

    // ------------------------------------------------------------------------

    final protected function parseAction( Action $action, array $uriSegments = [] )
    {
        if ( $closure = $action->getClosure() ) {
            if ( $closure instanceof Controller ) {
                $controllerRegistry = new Router\Datastructures\Controller( $closure );
                $uriSegments = empty( $uriSegments )
                    ? $action->getClosureParameters()
                    : $uriSegments;
                $this->setController( $controllerRegistry, $uriSegments );
            } elseif ( $closure instanceof Router\Datastructures\Controller ) {
                $this->setController( $closure, $action->getClosureParameters() );
            } elseif ( is_array( $closure ) ) {
                $uri = ( new \O2System\Framework\Http\Message\Uri() )
                    ->withSegments( new \O2System\Framework\Http\Message\Uri\Segments( '' ) )
                    ->withQuery( '' );
                $this->parseRequest( $uri->addSegments( $closure ) );
            }
        } else {
            output()->sendError( 204 );
        }
    }

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
            if ( $controller->hasMethod( 'route' ) ) {
                $controller
                    ->setRequestMethod( 'route' )
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

                    // remove unused parameters
                    $controllerMethodParams = array_slice( $controllerMethodParams, 0,
                        $index->getNumberOfParameters() );

                    $controller
                        ->setRequestMethod( 'index' )
                        ->setRequestMethodArgs( $controllerMethodParams );
                } else {
                    output()->sendError( 404 );
                }
            }
        } elseif ( $controller->hasMethod( 'route' ) ) {
            $controller
                ->setRequestMethod( 'route' )
                ->setRequestMethodArgs( [ 'index', [] ] );
        } elseif ( $controller->hasMethod( 'index' ) ) {
            $controller
                ->setRequestMethod( 'index' );
        }

        // Set Router Controller
        o2system()->addService( $controller, 'controller' );
    }

    // ------------------------------------------------------------------------

    final protected function setPage( Page $page )
    {
        foreach ( modules()->getNamespaces() as $controllersNamespace ) {
            $controllerPagesClassName = $controllersNamespace->name . 'Controllers\Pages';

            if ( $controllersNamespace->name === 'O2System\Framework\\' ) {
                $controllerPagesClassName = 'O2System\Framework\Http\Controllers\Pages';
            }

            if ( class_exists( $controllerPagesClassName ) ) {
                $controller = new $controllerPagesClassName();

                if ( method_exists( $controller, 'setPage' ) ) {
                    $controller->setPage( $page );

                    $this->setController(
                        ( new Router\Datastructures\Controller( $controller ) )
                            ->setRequestMethod( 'index' )
                    );

                    return true;
                }

                break;
            }
        }

        return false;
    }
}