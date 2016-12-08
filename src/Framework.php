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

    /**
     * Framework::__construct
     *
     * @return Framework
     */
    protected function __construct ()
    {
        parent::__construct();

        profiler()->watch( 'SYSTEM_START' );

        profiler()->watch( 'INSTANTIATE_HOOKS_SERVICE' );
        $hooks = new Framework\Services\Hooks();

        profiler()->watch( 'CALL_HOOKS_PRE_SYSTEM' );
        $hooks->callEvent( Framework\Services\Hooks::PRE_SYSTEM );

        profiler()->watch( 'INSTANTIATE_SYSTEM_SERVICES' );

        foreach ( [ 'Loader', 'Config' ] as $serviceClassName ) {
            if ( class_exists( 'App\Kernel\Services\\' . $serviceClassName ) ) {
                $this->addService( new Kernel\Registries\Service( 'App\Kernel\Services\\' . $serviceClassName ) );
            } elseif ( class_exists( 'O2System\Framework\Services\\' . $serviceClassName ) ) {
                $this->addService(
                    new Kernel\Registries\Service( 'O2System\Framework\Services\\' . $serviceClassName )
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

                $this->database = new Database\ConnectionPools(
                    new Database\Registries\Config(
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

    public function __isset ( $property )
    {
        return (bool) isset( $this->{$property} );
    }

    // ------------------------------------------------------------------------

    public function &__get ( $property )
    {
        $get[ $property ] = false;

        if ( isset( $this->{$property} ) ) {
            return $this->{$property};
        }

        return $get[ $property ];
    }

    // ------------------------------------------------------------------------

    protected function __reconstruct ()
    {
        // Instantiate Modules Container
        profiler()->watch( 'INSTANTIATE_MODULES_CONTAINER' );
        $this->modules = new Framework\Containers\Modules();

        // Instantiate Cache Service
        profiler()->watch( 'INSTANTIATE_CACHE_SERVICE' );
        $cache = new Cache\ItemPools( config( 'cache', true ) );
        $this->addService( $cache, 'cache' );

        // Modules Service Load Registries
        profiler()->watch( 'MODULES_SERVICE_LOAD_REGISTRIES' );
        modules()->loadRegistry();

        // Languages Service Load Registries
        profiler()->watch( 'LANGUAGES_SERVICE_LOAD_REGISTRIES' );
        language()->loadRegistry();

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
     * Framework::cliRoute
     *
     * @return void
     */
    private function cliHandler ()
    {
        $router = new Framework\Cli\Router();
        $this->addService( $router );
    }

    // ------------------------------------------------------------------------

    /**
     * Framework::httpRoute
     *
     * @return void
     */
    private function httpHandler ()
    {
        // Instantiate Session Service
        profiler()->watch( 'INSTANTIATE_SESSION_SERVICE' );
        $session = new Session( config( 'session', true ) );

        if ( ! $session->isStarted() ) {
            $session->start();
        }

        $this->addService( $session );

        // Instantiate Http View Service
        profiler()->watch( 'INSTANTIATE_HTTP_PARSER_SERVICE' );
        $this->addService( 'O2System\Framework\Http\Parser' );

        // Instantiate Http View Service
        profiler()->watch( 'INSTANTIATE_HTTP_VIEW_SERVICE' );
        $this->addService( 'O2System\Framework\Http\View' );

        // Instantiate Http Presenter Service
        $this->addService( 'O2System\Framework\Http\Presenter' );

        // Instantiate Http Middleware Service
        profiler()->watch( 'INSTANTIATE_HTTP_MIDDLEWARE_SERVICE' );
        $this->addService( 'O2System\Framework\Http\Middleware' );

        // Instantiate Http Router Service
        profiler()->watch( 'INSTANTIATE_HTTP_ROUTER_SERVICE' );
        $this->addService( 'O2System\Framework\Http\Router' );

        profiler()->watch( 'HTTP_ROUTER_SERVICE_PARSE_REQUEST' );
        router()->parseRequest( new Framework\Http\Message\Request() );

        profiler()->watch( 'HTTP_RUN_MIDDLEWARE_SERVICE' );
        middleware()->run();

        if ( $controller = router()->getController() ) {

            if ( $controller instanceof Framework\Http\Router\Registries\Controller ) {

                // Autoload Model and Assets
                $controllerAssets = [ ];

                foreach ( modules() as $module ) {
                    if ( in_array( $module->getType(), [ 'KERNEL', 'FRAMEWORK' ] ) ) {
                        continue;
                    }

                    $controllerAssets[] = $module->getParameter();
                    $module->loadModel();
                }

                $modelClassName = str_replace( 'Controllers', 'Models', $controller->getName() );

                if ( class_exists( $modelClassName ) ) {
                    models()->register( 'controller', new $modelClassName() );
                }

                if ( $controller = router()->getController() ) {
                    $controllerAssets[] = $controller->getParameter();
                    $controllerAssets[] = $controller->getRequestMethod();
                } else {
                    $controllerAssets[] = get_class_name( $this );
                }

                // Decamelcase assets file names
                $controllerAssets = array_map( 'decamelcase', $controllerAssets );

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
                    $this->addService( new $presenterClassName(), 'presenter' );
                }

                presenter()->assets->loadItems(
                    [
                        'css' => $controllerAssets,
                        'js'  => $controllerAssets,
                    ]
                );

                profiler()->watch( 'CALL_HOOKS_PRE_CONTROLLER' );
                hooks()->callEvent( Framework\Services\Hooks::PRE_CONTROLLER );

                profiler()->watch( 'INSTANTIATE_REQUESTED_CONTROLLER' );
                $requestController = $controller->getInstance();

                $this->addService( $requestController, 'controller' );

                profiler()->watch( 'CALL_HOOKS_POST_CONTROLLER' );
                hooks()->callEvent( Framework\Services\Hooks::POST_CONTROLLER );

                $requestMethod = $controller->getRequestMethod();
                $requestMethodArgs = $controller->getRequestMethodArgs();

                profiler()->watch( 'HTTP_RUN_CONTROLLER_MIDDLEWARE_SERVICE' );
                middleware()->run();

                // Call the requested controller method
                profiler()->watch( 'CALL_REQUESTED_CONTROLLER_METHOD' );
                $requestController->__call( $requestMethod, $requestMethodArgs );

                profiler()->watch( 'VIEW_SERVICE_RENDER' );
                view()->render();

                exit( EXIT_SUCCESS );
            }
        }

        middleware()->run();

        // Show Error (404) Page Not Found
        output()->showError( 404, 'NOT_FOUND_HEADER', 'NOT_FOUND_MESSAGE' );
    }
}