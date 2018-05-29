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
switch (strtoupper(ENVIRONMENT)) {
    case 'DEVELOPMENT':
        error_reporting(-1);
        ini_set('display_errors', 1);
        break;
    case 'TESTING':
    case 'PRODUCTION':
        ini_set('display_errors', 0);
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        break;
    default:
        header('HTTP/1.1 503 Service Unavailable.', true, 503);
        echo 'The application environment is not set correctly.';
        exit(1); // EXIT_ERROR
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
if ( ! defined('PATH_VENDOR')) {
    define('PATH_VENDOR', PATH_ROOT . 'vendor' . DIRECTORY_SEPARATOR);
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
if ( ! defined('PATH_FRAMEWORK')) {
    define('PATH_FRAMEWORK', __DIR__ . DIRECTORY_SEPARATOR);
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
if ( ! defined('PATH_APP')) {
    define('PATH_APP', PATH_ROOT . DIR_APP . DIRECTORY_SEPARATOR);
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
if ( ! defined('PATH_PUBLIC')) {
    define('PATH_PUBLIC', PATH_ROOT . DIR_PUBLIC . DIRECTORY_SEPARATOR);
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
if ( ! defined('PATH_CACHE')) {
    define('PATH_CACHE', PATH_ROOT . DIR_CACHE . DIRECTORY_SEPARATOR);
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
if ( ! defined('PATH_STORAGE')) {
    define('PATH_STORAGE', PATH_ROOT . DIR_STORAGE . DIRECTORY_SEPARATOR);
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
     * Framework Database Connection Pools
     *
     * @var Database\Connections
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
        output()->addFilePath(PATH_APP);

        profiler()->watch('INSTANTIATE_HOOKS_SERVICE');
        $hooks = new Framework\Services\Hooks();

        profiler()->watch('CALL_HOOKS_PRE_SYSTEM');
        $hooks->callEvent(Framework\Services\Hooks::PRE_SYSTEM);

        profiler()->watch('SYSTEM_START');

        // Instantiate Globals Container
        profiler()->watch('INSTANTIATE_GLOBALS_CONTAINER');
        $this->addService(new Kernel\Containers\Globals(), 'globals');

        // Instantiate Environment Container
        profiler()->watch('INSTANTIATE_ENVIRONMENT_CONTAINER');
        $this->addService(new Kernel\Containers\Environment(), 'environment');

        profiler()->watch('INSTANTIATE_SYSTEM_SERVICES');

        foreach (['Loader', 'Config', 'Logger'] as $serviceClassName) {
            if (class_exists('App\Kernel\Services\\' . $serviceClassName)) {
                $this->addService(new Kernel\Datastructures\Service('App\Kernel\Services\\' . $serviceClassName));
            } elseif (class_exists('O2System\Framework\Services\\' . $serviceClassName)) {
                $this->addService(
                    new Kernel\Datastructures\Service('O2System\Framework\Services\\' . $serviceClassName)
                );
            }
        }

        $this->addService($hooks);

        if ($config = config()->loadFile('database', true)) {
            if ( ! empty($config[ 'default' ][ 'hostname' ]) AND ! empty($config[ 'default' ][ 'username' ])) {

                // Instantiate Database Connection Pools
                profiler()->watch('INSTANTIATE_DATABASE_CONNECTION_POOLS');

                $this->database = new Database\Connections(
                    new Database\Datastructures\Config(
                        $config->getArrayCopy()
                    )
                );
            }
        }

        // Instantiate Models Container
        profiler()->watch('INSTANTIATE_MODELS_CONTAINER');
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
    public function __isset($property)
    {
        return (bool)isset($this->{$property});
    }

    // ------------------------------------------------------------------------

    /**
     * Framework::__get
     *
     * @param $property
     *
     * @return mixed
     */
    public function &__get($property)
    {
        if (isset($this->{$property})) {
            return $this->{$property};
        }

        return $this->getService($property, true);
    }

    // ------------------------------------------------------------------------

    /**
     * Framework::__reconstruct
     *
     */
    protected function __reconstruct()
    {
        // Instantiate Modules Container
        profiler()->watch('INSTANTIATE_MODULES_CONTAINER');
        $this->modules = new Framework\Containers\Modules();

        // Instantiate Cache Service
        profiler()->watch('INSTANTIATE_CACHE_SERVICE');
        $cache = new Framework\Services\Cache(config('cache', true));
        $this->addService($cache, 'cache');

        // Modules Service Load Datastructures
        profiler()->watch('MODULES_SERVICE_LOAD_REGISTRIES');
        modules()->loadRegistry();

        // Languages Service Load Datastructures
        profiler()->watch('LANGUAGES_SERVICE_LOAD_REGISTRIES');
        language()->loadRegistry();

        // Modules default app
        if (null !== ($defaultApp = config('app'))) {
            if (false !== ($defaultModule = modules()->getApp($defaultApp))) {
                // Register Domain App Module Namespace
                loader()->addNamespace($defaultModule->getNamespace(), $defaultModule->getRealPath());

                // Push Domain App Module
                modules()->push($defaultModule);
            } elseif (false !== ($defaultModule = modules()->getModule($defaultApp))) {
                // Register Path Module Namespace
                loader()->addNamespace($defaultModule->getNamespace(), $defaultModule->getRealPath());

                // Push Path Module
                modules()->push($defaultModule);
            }
        }

        profiler()->watch('CALL_HOOKS_POST_SYSTEM');
        hooks()->callEvent(Framework\Services\Hooks::POST_SYSTEM);

        if (is_cli()) {
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
        profiler()->watch('INSTANTIATE_CLI_ROUTER_SERVICE');
        $this->addService('O2System\Framework\Cli\Router');

        profiler()->watch('CLI_ROUTER_SERVICE_PARSE_REQUEST');
        router()->parseRequest();

        if ($commander = router()->getCommander()) {
            if ($commander instanceof Kernel\Cli\Router\Datastructures\Commander) {
                // Autoload Language
                language()->loadFile($commander->getParameter());
                language()->loadFile($commander->getRequestMethod());
                language()->loadFile($commander->getParameter() . '/' . $commander->getRequestMethod());

                // Autoload Model
                $modelClassName = str_replace('Commanders', 'Models', $commander->getName());

                if (class_exists($modelClassName)) {
                    models()->register('commander', new $modelClassName());
                }

                profiler()->watch('INSTANTIATE_REQUESTED_COMMANDER');
                $requestCommander = $commander->getInstance();

                profiler()->watch('EXECUTE_REQUESTED_COMMANDER');
                $requestCommander->execute();

                exit(EXIT_SUCCESS);
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
        if (config('session', true)->enabled === true) {

            // Instantiate Session Service
            profiler()->watch('INSTANTIATE_SESSION_SERVICE');

            $session = new Session(config('session', true));
            $session->setLogger($this->getService('logger'));

            if ( ! $session->isStarted()) {
                $session->start();
            }

            $this->addService($session);
        }

        if (config('view', true)->enabled === true) {
            // Instantiate Http View Service
            profiler()->watch('INSTANTIATE_HTTP_PARSER_SERVICE');
            $this->addService('O2System\Framework\Http\Parser');

            // Instantiate Http View Service
            profiler()->watch('INSTANTIATE_HTTP_VIEW_SERVICE');
            $this->addService('O2System\Framework\Http\View');
        }

        if (config('presenter', true)->enabled === true) {
            // Instantiate Http Presenter Service
            profiler()->watch('INSTANTIATE_HTTP_PRESENTER_SERVICE');
            $this->addService('O2System\Framework\Http\Presenter');
            presenter()->initialize();
        }

        $this->addService('O2System\Framework\Http\Message\ServerRequest');

        // Instantiate Http Middleware Service
        profiler()->watch('INSTANTIATE_HTTP_MIDDLEWARE_SERVICE');
        $this->addService('O2System\Framework\Http\Middleware');

        // Instantiate Http Router Service
        profiler()->watch('INSTANTIATE_HTTP_ROUTER_SERVICE');
        $this->addService('O2System\Framework\Http\Router');

        profiler()->watch('HTTP_ROUTER_SERVICE_PARSE_REQUEST');
        router()->parseRequest();

        profiler()->watch('HTTP_RUN_MIDDLEWARE_SERVICE');
        middleware()->run();

        if (false !== ($controller = $this->getService('controller'))) {
            if ($controller instanceof Kernel\Http\Router\Datastructures\Controller) {

                $controllerParameter = dash($controller->getParameter());
                $controllerRequestMethod = dash($controller->getRequestMethod());

                // Autoload Language
                language()->loadFile($controller->getParameter());
                language()->loadFile($controller->getRequestMethod());
                language()->loadFile($controller->getParameter() . '/' . $controller->getRequestMethod());

                // Autoload Model
                foreach (modules() as $module) {
                    if (in_array($module->getType(), ['KERNEL', 'FRAMEWORK'])) {
                        continue;
                    }
                    $module->loadModel();
                }

                // Load App Module Models
                $app = modules()->first();
                $app->loadModel();

                // Load Current Module Models
                $module = modules()->current();
                $module->loadModel();

                // Autoload Model
                $modelClassName = str_replace(['Controllers', 'Presenters'], 'Models', $controller->getName());

                if (class_exists($modelClassName)) {
                    models()->register('controller', new $modelClassName());
                }

                // Autoload Assets
                if (config('presenter', true)->enabled === true) {
                    $controllerAssets = [];
                    $controllerAssetsDirs = [];

                    // Load App Module Assets
                    $controllerAssets[] = $app->getParameter();
                    $controllerAssetsDirs[] = $app->getParameter();

                    // Load Current Module Assets
                    $controllerAssets[] = $module->getParameter();
                    $controllerAssetsDirs[] = $module->getParameter();

                    $controllerAssets = array_reverse($controllerAssets);

                    $controllerAssets[] = $controllerParameter;
                    $controllerAssetsDirs[] = $controllerParameter;

                    $controllerAssets[] = $controllerRequestMethod;

                    foreach ($controllerAssetsDirs as $controllerAssetsDir) {
                        $controllerAssets[] = $controllerAssetsDir . '/' . $controllerParameter;
                        $controllerAssets[] = $controllerAssetsDir . '/' . $controllerRequestMethod;
                        $controllerAssets[] = $controllerAssetsDir . '/' . $controllerParameter . '/' . $controllerRequestMethod;
                    }

                    // Autoload Presenter
                    $presenterClassName = str_replace('Controllers', 'Presenters', $controller->getName());

                    if (class_exists($presenterClassName)) {
                        $presenterClassObject = new $presenterClassName();
                        if ($presenterClassObject instanceof Framework\Http\Presenter) {
                            $this->addService(new $presenterClassName(), 'presenter');
                        }
                    }

                    // Re-Initialize Presenter
                    presenter()->initialize()->assets->loadFiles(
                        [
                            'css' => $controllerAssets,
                            'js'  => $controllerAssets,
                        ]
                    );
                }

                // Initialize Controller
                profiler()->watch('CALL_HOOKS_PRE_CONTROLLER');
                hooks()->callEvent(Framework\Services\Hooks::PRE_CONTROLLER);

                profiler()->watch('INSTANTIATE_REQUESTED_CONTROLLER');
                $requestController = $controller->getInstance();

                if (method_exists($requestController, '__reconstruct')) {
                    $requestController->__reconstruct();
                } elseif (method_exists($requestController, 'initialize')) {
                    $requestController->initialize();
                }

                profiler()->watch('HTTP_RUN_CONTROLLER_MIDDLEWARE_SERVICE');
                middleware()->run();

                $this->addService($requestController, 'controller');

                profiler()->watch('CALL_HOOKS_POST_CONTROLLER');
                hooks()->callEvent(Framework\Services\Hooks::POST_CONTROLLER);

                $requestMethod = $controller->getRequestMethod();
                $requestMethodArgs = $controller->getRequestMethodArgs();

                // Call the requested controller method
                profiler()->watch('CALL_REQUESTED_CONTROLLER_METHOD');
                ob_start();
                $requestControllerOutput = $requestController->__call($requestMethod, $requestMethodArgs);

                profiler()->watch('END_REQUESTED_CONTROLLER_METHOD');

                if (empty($requestControllerOutput)) {
                    $requestControllerOutput = ob_get_contents();
                    ob_end_clean();
                } elseif (is_bool($requestControllerOutput)) {
                    if ($requestController instanceof Framework\Http\Controllers\Restful) {
                        $requestController->sendError($requestControllerOutput);
                    } else {
                        if ($requestControllerOutput === true) {
                            output()->sendError(200);
                            exit(EXIT_SUCCESS);
                        } elseif ($requestControllerOutput === false) {
                            output()->sendError(204);
                            exit(EXIT_ERROR);
                        }
                    }
                } elseif (is_array($requestControllerOutput) || is_object($requestControllerOutput)) {
                    if ($requestController instanceof Framework\Http\Controllers\Restful) {
                        $requestController->sendPayload($requestControllerOutput);
                    } else {
                        output()->send($requestControllerOutput);
                    }
                } elseif (is_numeric($requestControllerOutput)) {
                    output()->sendError($requestControllerOutput);
                }

                if (empty($requestControllerOutput) || $requestControllerOutput === '') {
                    if ($requestController instanceof Framework\Http\Controllers\Restful) {
                        $requestController->sendError(204);
                    } elseif (config('presenter', true)->enabled === true) {
                        $filenames = [
                            $controllerRequestMethod,
                            $controllerParameter . DIRECTORY_SEPARATOR . $controllerRequestMethod,
                        ];

                        if ($controllerRequestMethod === 'index') {
                            array_unshift($filenames, $controllerParameter);
                        }

                        profiler()->watch('VIEW_RENDER_START');

                        foreach ($filenames as $filename) {
                            if (view()->getFilePath($filename)) {
                                view()->load($filename);
                            }
                        }

                        if (presenter()->partials->offsetExists('content')) {
                            view()->render();
                            profiler()->watch('VIEW_RENDER_END');
                            exit(EXIT_SUCCESS);
                        }
                    }

                    // Send default error 204 - No Content
                    output()->sendError(204);
                } elseif (is_string($requestControllerOutput)) {
                    if (is_json($requestControllerOutput)) {
                        output()->setContentType('application/json');
                        output()->send($requestControllerOutput);
                    } elseif (is_serialized($requestControllerOutput)) {
                        output()->send($requestControllerOutput);
                    } elseif (config('presenter', true)->enabled === true) {
                        presenter()->partials->offsetSet('content', $requestControllerOutput);

                        profiler()->watch('VIEW_RENDER_END');
                        view()->render();
                    } else {
                        output()->send($requestControllerOutput);
                    }
                    exit(EXIT_SUCCESS);
                }
            }
        }

        middleware()->run();

        // Show Error (404) Page Not Found
        output()->sendError(404);
    }
}