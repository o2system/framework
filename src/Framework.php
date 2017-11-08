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

namespace O2System;

// ------------------------------------------------------------------------

/*
 * ---------------------------------------------------------------
 * ERROR REPORTING
 * ---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but testing and live will hide them.
 */
switch ( strtoupper( ENVIRONMENT ) ) {
    case 'DEVELOPMENT':
        error_reporting( -1 );
        ini_set( 'display_errors', 1 );
        break;
    case 'TESTING':
    case 'PRODUCTION':
        ini_set( 'display_errors', 0 );
        error_reporting( E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED );
        break;
    default:
        header( 'HTTP/1.1 503 Service Unavailable.', true, 503 );
        echo 'The application environment is not set correctly.';
        exit( 1 ); // EXIT_ERROR
}

/*
 *---------------------------------------------------------------
 * VENDOR PATH
 *---------------------------------------------------------------
 *
 * RealPath to vendor folder.
 *
 * WITH TRAILING SLASH!
 */
if ( ! defined( 'PATH_VENDOR' ) ) {
    define( 'PATH_VENDOR', PATH_ROOT . 'vendor' . DIRECTORY_SEPARATOR );
}

/*
 *---------------------------------------------------------------
 * FRAMEWORK PATH
 *---------------------------------------------------------------
 *
 * RealPath to framework folder.
 *
 * WITH TRAILING SLASH!
 */
if ( ! defined( 'PATH_FRAMEWORK' ) ) {
    define( 'PATH_FRAMEWORK', __DIR__ . DIRECTORY_SEPARATOR );
}

/*
 *---------------------------------------------------------------
 * APP PATH
 *---------------------------------------------------------------
 *
 * RealPath to application folder.
 *
 * WITH TRAILING SLASH!
 */
if ( ! defined( 'PATH_APP' ) ) {
    define( 'PATH_APP', PATH_ROOT . DIR_APP . DIRECTORY_SEPARATOR );
}

/*
 *---------------------------------------------------------------
 * PUBLIC PATH
 *---------------------------------------------------------------
 *
 * RealPath to public folder.
 *
 * WITH TRAILING SLASH!
 */
if ( ! defined( 'PATH_PUBLIC' ) ) {
    define( 'PATH_PUBLIC', PATH_ROOT . DIR_PUBLIC . DIRECTORY_SEPARATOR );
}

/*
 *---------------------------------------------------------------
 * CACHE PATH
 *---------------------------------------------------------------
 *
 * RealPath to writable caching folder.
 *
 * WITH TRAILING SLASH!
 */
if ( ! defined( 'PATH_CACHE' ) ) {
    define( 'PATH_CACHE', PATH_ROOT . DIR_CACHE . DIRECTORY_SEPARATOR );
}

/*
 *---------------------------------------------------------------
 * STORAGE PATH
 *---------------------------------------------------------------
 *
 * RealPath to writable storage folder.
 *
 * WITH TRAILING SLASH!
 */
if ( ! defined( 'PATH_STORAGE' ) ) {
    define( 'PATH_STORAGE', PATH_ROOT . DIR_STORAGE . DIRECTORY_SEPARATOR );
}

/*
 *---------------------------------------------------------------
 * FRAMEWORK CONSTANTS
 *---------------------------------------------------------------
 */
require __DIR__ . '/Config/Constants.php';

/*
 *---------------------------------------------------------------
 * FRAMEWORK HELPERS
 *---------------------------------------------------------------
 */
require __DIR__ . '/Helpers/Framework.php';

/**
 * Class Framework
 *
 * @package O2System
 */
class Framework extends Kernel
{
    /**
     * Framework Container Globals
     *
     * @var Framework\Containers\Globals
     */
    private $globals;

    /**
     * Framework Database Connection Pools
     *
     * @var Framework\Containers\Globals
     */
    private $database;

    /**
     * Framework Container Models
     *
     * @var Framework\Containers\Models
     */
    private $models;

    /**
     * Framework Container Modules
     *
     * @var Framework\Containers\Modules
     */
    private $modules;

    // ------------------------------------------------------------------------

    /**
     * Framework::__construct
     *
     * @return Framework
     */
    protected function __construct()
    {
        parent::__construct();

        // Add App Views Folder
        output()->addFilePath( PATH_APP );

        profiler()->watch( 'SYSTEM_START' );

        profiler()->watch( 'INSTANTIATE_HOOKS_SERVICE' );
        $hooks = new Framework\Services\Hooks();

        profiler()->watch( 'CALL_HOOKS_PRE_SYSTEM' );
        $hooks->callEvent( Framework\Services\Hooks::PRE_SYSTEM );

        profiler()->watch( 'INSTANTIATE_SYSTEM_SERVICES' );

        foreach ( [ 'Loader', 'Config' ] as $serviceClassName ) {
            if ( class_exists( 'App\Kernel\Services\\' . $serviceClassName ) ) {
                $this->addService( new Kernel\Datastructures\Service( 'App\Kernel\Services\\' . $serviceClassName ) );
            } elseif ( class_exists( 'O2System\Framework\Services\\' . $serviceClassName ) ) {
                $this->addService(
                    new Kernel\Datastructures\Service( 'O2System\Framework\Services\\' . $serviceClassName )
                );
            }
        }

        $this->addService( $hooks );

        // Instantiate Globals Container
        profiler()->watch( 'INSTANTIATE_GLOBALS_CONTAINER' );
        $this->globals = new Framework\Containers\Globals();

        if ( $config = config()->loadFile( 'database', true ) ) {
            if ( ! empty( $config[ 'default' ][ 'hostname' ] ) AND ! empty( $config[ 'default' ][ 'username' ] ) ) {

                // Instantiate Database Connection Pools
                profiler()->watch( 'INSTANTIATE_DATABASE_CONNECTION_POOLS' );

                $this->database = new Database\Connections(
                    new Database\Datastructures\Config(
                        $config->getArrayCopy()
                    )
                );
            }
        }

        // Instantiate Models Container
        profiler()->watch( 'INSTANTIATE_MODELS_CONTAINER' );
        $this->models = new Framework\Containers\Models();
    }

    // ------------------------------------------------------------------------

    /**
     * Framework::__isset
     *
     * @param $property
     *
     * @return bool
     */
    public function __isset( $property )
    {
        return (bool) isset( $this->{$property} );
    }

    // ------------------------------------------------------------------------

    /**
     * Framework::__get
     *
     * @param $property
     *
     * @return mixed
     */
    public function &__get( $property )
    {
        $get[ $property ] = false;

        if ( isset( $this->{$property} ) ) {
            return $this->{$property};
        }

        return $get[ $property ];
    }

    // ------------------------------------------------------------------------

    /**
     * Framework::__reconstruct
     *
     */
    protected function __reconstruct()
    {
        // Instantiate Modules Container
        profiler()->watch( 'INSTANTIATE_MODULES_CONTAINER' );
        $this->modules = new Framework\Containers\Modules();

        // Instantiate Cache Service
        profiler()->watch( 'INSTANTIATE_CACHE_SERVICE' );
        $cache = new Cache\Adapters( config( 'cache', true ) );
        $this->addService( $cache, 'cache' );

        // Modules Service Load Datastructures
        profiler()->watch( 'MODULES_SERVICE_LOAD_REGISTRIES' );
        modules()->loadRegistry();

        // Languages Service Load Datastructures
        profiler()->watch( 'LANGUAGES_SERVICE_LOAD_REGISTRIES' );
        language()->loadRegistry();

        // Modules default app
        if ( null !== ( $defaultApp = config( 'app' ) ) ) {
            if ( false !== ( $defaultModule = modules()->getApp( $defaultApp ) ) ) {
                // Register Domain App Module Namespace
                loader()->addNamespace( $defaultModule->getNamespace(), $defaultModule->getRealPath() );

                // Push Domain App Module
                modules()->push( $defaultModule );
            } elseif ( false !== ( $defaultModule = modules()->getModule( $defaultApp ) ) ) {
                // Register Path Module Namespace
                loader()->addNamespace( $defaultModule->getNamespace(), $defaultModule->getRealPath() );

                // Push Path Module
                modules()->push( $defaultModule );
            }
        }

        profiler()->watch( 'CALL_HOOKS_POST_SYSTEM' );
        hooks()->callEvent( Framework\Services\Hooks::POST_SYSTEM );

        if ( is_cli() ) {
            $this->cliHandler();
        } else {
            $this->httpHandler();
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Framework::cliHandler
     *
     * @return void
     */
    private function cliHandler()
    {
        // Instantiate CLI Router Service
        profiler()->watch( 'INSTANTIATE_CLI_ROUTER_SERVICE' );
        $this->addService( 'O2System\Framework\Cli\Router' );

        profiler()->watch( 'CLI_ROUTER_SERVICE_PARSE_REQUEST' );
        router()->parseRequest();

        if ( $commander = router()->getCommander() ) {
            if ( $commander instanceof Framework\Cli\Router\Datastructures\Commander ) {
                profiler()->watch( 'INSTANTIATE_REQUESTED_COMMANDER' );
                $requestCommander = $commander->getInstance();

                profiler()->watch( 'EXECUTE_REQUESTED_COMMANDER' );
                $requestCommander->execute();

                exit( EXIT_SUCCESS );
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Framework::httpHandler
     *
     * @return void
     */
    private function httpHandler()
    {
        if ( config( 'session', true )->enabled === true ) {

            // Instantiate Session Service
            profiler()->watch( 'INSTANTIATE_SESSION_SERVICE' );

            $session = new Session( config( 'session', true ) );

            if ( ! $session->isStarted() ) {
                $session->start();
            }

            $this->addService( $session );
        }

        if ( config( 'view', true )->enabled === true ) {
            // Instantiate Http View Service
            profiler()->watch( 'INSTANTIATE_HTTP_PARSER_SERVICE' );
            $this->addService( 'O2System\Framework\Http\Parser' );

            // Instantiate Http View Service
            profiler()->watch( 'INSTANTIATE_HTTP_VIEW_SERVICE' );
            $this->addService( 'O2System\Framework\Http\View' );
        }

        if ( config( 'presenter', true )->enabled === true ) {
            // Instantiate Http Presenter Service
            profiler()->watch( 'INSTANTIATE_HTTP_PRESENTER_SERVICE' );
            $this->addService( 'O2System\Framework\Http\Presenter' );
        }

        $this->addService( 'O2System\Framework\Http\Message\Request' );

        // Instantiate Http Middleware Service
        profiler()->watch( 'INSTANTIATE_HTTP_MIDDLEWARE_SERVICE' );
        $this->addService( 'O2System\Framework\Http\Middleware' );

        // Instantiate Http Router Service
        profiler()->watch( 'INSTANTIATE_HTTP_ROUTER_SERVICE' );
        $this->addService( 'O2System\Framework\Http\Router' );

        profiler()->watch( 'HTTP_ROUTER_SERVICE_PARSE_REQUEST' );
        router()->parseRequest();

        profiler()->watch( 'HTTP_RUN_MIDDLEWARE_SERVICE' );
        middleware()->run();

        if ( false !== ( $controller = $this->getService( 'controller' ) ) ) {

            if ( $controller instanceof Framework\Http\Router\Datastructures\Controller ) {

                // Autoload Language
                language()->loadFile( $controller->getParameter() );
                language()->loadFile( $controller->getRequestMethod() );
                language()->loadFile( $controller->getParameter() . '/' . $controller->getRequestMethod() );

                if ( config( 'presenter', true )->enabled === true ) {
                    // Autoload Model and Assets
                    $controllerAssets = [];

                    foreach ( modules() as $module ) {
                        if ( in_array( $module->getType(), [ 'KERNEL', 'FRAMEWORK' ] ) ) {
                            continue;
                        }

                        $controllerAssets[] = $module->getParameter();
                        $module->loadModel();
                    }

                    $controllerAssets = array_reverse( $controllerAssets );

                    // Autoload Model
                    $modelClassName = str_replace( [ 'Controllers', 'Presenters' ], 'Models', $controller->getName() );

                    if ( class_exists( $modelClassName ) ) {
                        models()->register( 'controller', new $modelClassName() );
                    }

                    $controllerAssets[] = $controller->getParameter();
                    $controllerAssets[] = $controller->getRequestMethod();

                    // Snakecase assets file names
                    $controllerAssets = array_map( 'snakecase', $controllerAssets );

                    // Dashed assets file names
                    $controllerAssets = array_map( 'dash', $controllerAssets );

                    $numFileNames = count( $controllerAssets );

                    for ( $i = 0; $i < $numFileNames; $i++ ) {
                        $assetFilename = array_slice( $controllerAssets, 0, ( $numFileNames - $i ) );
                        $assetFilename = implode( DIRECTORY_SEPARATOR, $assetFilename );

                        if ( ! in_array( $assetFilename, $controllerAssets ) ) {
                            $controllerAssets[] = $assetFilename;
                        }
                    }

                    // Autoload Presenter
                    $presenterClassName = str_replace( 'Controllers', 'Presenters', $controller->getName() );

                    if ( class_exists( $presenterClassName ) ) {
                        $presenterClassObject = new $presenterClassName();
                        if ( $presenterClassObject instanceof Framework\Http\Presenter ) {
                            $this->addService( new $presenterClassName(), 'presenter' );
                        }
                    }

                    // Re-Initialize Presenter
                    presenter()->initialize()->assets->loadFiles(
                        [
                            'css' => $controllerAssets,
                            'js'  => $controllerAssets,
                        ]
                    );

                    profiler()->watch( 'CALL_HOOKS_PRE_CONTROLLER' );
                    hooks()->callEvent( Framework\Services\Hooks::PRE_CONTROLLER );

                    profiler()->watch( 'INSTANTIATE_REQUESTED_CONTROLLER' );
                    $requestController = $controller->getInstance();

                    if ( method_exists( $requestController, '__reconstruct' ) ) {
                        $requestController->__reconstruct();
                    } elseif ( method_exists( $requestController, 'initialize' ) ) {
                        $requestController->initialize();
                    }

                    $this->addService( $requestController, 'controller' );

                    profiler()->watch( 'CALL_HOOKS_POST_CONTROLLER' );
                    hooks()->callEvent( Framework\Services\Hooks::POST_CONTROLLER );

                    $requestMethod = $controller->getRequestMethod();
                    $requestMethodArgs = $controller->getRequestMethodArgs();

                    profiler()->watch( 'HTTP_RUN_CONTROLLER_MIDDLEWARE_SERVICE' );
                    middleware()->run();

                    // Call the requested controller method
                    profiler()->watch( 'CALL_REQUESTED_CONTROLLER_METHOD' );
                    ob_start();
                    $requestControllerOutput = $requestController->__call( $requestMethod, $requestMethodArgs );

                    if ( $requestControllerOutput === true ) {
                        output()->sendError( 200 );
                        exit( EXIT_SUCCESS );
                    } elseif ( $requestControllerOutput === false ) {
                        output()->sendError( 204 );
                        exit( EXIT_ERROR );
                    } elseif ( is_array( $requestControllerOutput ) || is_object( $requestControllerOutput ) ) {
                        output()->send( $requestControllerOutput );
                        exit( EXIT_SUCCESS );
                    } elseif ( is_null( $requestControllerOutput ) ) {
                        $requestControllerOutput = ob_get_contents();
                        ob_end_clean();

                        if ( empty( $requestControllerOutput ) ) {
                            $requestControllerOutput = '';
                        } else {
                            echo $requestControllerOutput;
                            exit( EXIT_SUCCESS );
                        }
                    }

                    if ( is_string( $requestControllerOutput ) ) {
                        if ( presenter()->theme->use === true ) {
                            if ( ! presenter()->partials->offsetExists( 'content' ) && $requestControllerOutput !== '' ) {
                                presenter()->partials->offsetSet( 'content', $requestControllerOutput );
                            }

                            if ( presenter()->partials->offsetExists( 'content' ) ) {
                                profiler()->watch( 'VIEW_SERVICE_RENDER' );
                                view()->render();
                                exit( EXIT_SUCCESS );
                            } else {
                                output()->sendError( 204 );
                                exit( EXIT_ERROR );
                            }
                        } elseif ( $requestControllerOutput !== '' ) {
                            output()->send( $requestControllerOutput );
                            exit( EXIT_SUCCESS );
                        } else {
                            output()->sendError( 204 );
                            exit( EXIT_ERROR );
                        }

                    } elseif ( is_numeric( $requestControllerOutput ) ) {
                        output()->sendError( $requestControllerOutput );
                        exit( EXIT_ERROR );
                    }
                } else {
                    // Autoload Model
                    $controllerAssets = [];

                    foreach ( modules() as $module ) {
                        if ( in_array( $module->getType(), [ 'KERNEL', 'FRAMEWORK' ] ) ) {
                            continue;
                        }

                        $module->loadModel();
                    }

                    $controllerAssets = array_reverse( $controllerAssets );

                    // Autoload Model
                    $modelClassName = str_replace( 'Controllers', 'Models', $controller->getName() );

                    if ( class_exists( $modelClassName ) ) {
                        models()->register( 'controller', new $modelClassName() );
                    }

                    profiler()->watch( 'CALL_HOOKS_PRE_CONTROLLER' );
                    hooks()->callEvent( Framework\Services\Hooks::PRE_CONTROLLER );

                    profiler()->watch( 'INSTANTIATE_REQUESTED_CONTROLLER' );
                    $requestController = $controller->getInstance();

                    if ( method_exists( $requestController, '__reconstruct' ) ) {
                        $requestController->__reconstruct();
                    } elseif ( method_exists( $requestController, 'initialize' ) ) {
                        $requestController->initialize();
                    }

                    $this->addService( $requestController, 'controller' );

                    profiler()->watch( 'CALL_HOOKS_POST_CONTROLLER' );
                    hooks()->callEvent( Framework\Services\Hooks::POST_CONTROLLER );

                    $requestMethod = $controller->getRequestMethod();
                    $requestMethodArgs = $controller->getRequestMethodArgs();

                    profiler()->watch( 'HTTP_RUN_CONTROLLER_MIDDLEWARE_SERVICE' );
                    middleware()->run();

                    // Call the requested controller method
                    profiler()->watch( 'CALL_REQUESTED_CONTROLLER_METHOD' );
                    ob_start();
                    $requestControllerOutput = $requestController->__call( $requestMethod, $requestMethodArgs );

                    if ( $requestControllerOutput === true ) {
                        output()->sendError( 200 );
                        exit( EXIT_SUCCESS );
                    } elseif ( $requestControllerOutput === false ) {
                        output()->sendError( 204 );
                        exit( EXIT_ERROR );
                    } elseif ( is_array( $requestControllerOutput ) || is_object( $requestControllerOutput ) ) {
                        output()->send( $requestControllerOutput );
                        exit( EXIT_SUCCESS );
                    } elseif ( is_null( $requestControllerOutput ) ) {
                        $requestControllerOutput = ob_get_contents();
                        ob_end_clean();

                        if ( empty( $requestControllerOutput ) ) {
                            $requestControllerOutput = '';
                        } else {
                            echo $requestControllerOutput;
                            exit( EXIT_SUCCESS );
                        }
                    }

                    if ( is_string( $requestControllerOutput ) ) {
                        if ( $requestControllerOutput !== '' ) {
                            output()->send( $requestControllerOutput );
                            exit( EXIT_SUCCESS );
                        } else {
                            output()->sendError( 204 );
                            exit( EXIT_ERROR );
                        }

                    } elseif ( is_numeric( $requestControllerOutput ) ) {
                        output()->sendError( $requestControllerOutput );
                        exit( EXIT_ERROR );
                    }
                }
            }
        }

        middleware()->run();

        // Show Error (404) Page Not Found
        output()->sendError( 404 );
    }
}