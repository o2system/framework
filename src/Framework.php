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
 * RESOURCES PATH
 *---------------------------------------------------------------
 *
 * RealPath to writable resources folder.
 *
 * WITH TRAILING SLASH!
 */
if ( ! defined('PATH_RESOURCES')) {
    define('PATH_RESOURCES', PATH_ROOT . DIR_RESOURCES . DIRECTORY_SEPARATOR);
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
     * Framework Container Models
     *
     * @var Framework\Containers\Models
     */
    public $models;

    /**
     * Framework Container Modules
     *
     * @var Framework\Containers\Modules
     */
    public $modules;

    // ------------------------------------------------------------------------

    /**
     * Framework::__construct
     *
     * @return Framework
     */
    protected function __construct()
    {
        parent::__construct();

        if (profiler() !== false) {
            profiler()->watch('Starting O2System Framework');
        }

        // Add App Views Folder
        output()->addFilePath(PATH_APP);

        $this->services->load(Framework\Services\Hooks::class);

        if (profiler() !== false) {
            profiler()->watch('Starting Framework Services');
        }

        foreach (['Globals', 'Environment', 'Loader', 'Config'] as $serviceClassName) {
            if (class_exists('App\Kernel\Services\\' . $serviceClassName)) {
                $this->services->load('App\Kernel\Services\\' . $serviceClassName);
            } elseif (class_exists('O2System\Framework\Services\\' . $serviceClassName)) {
                $this->services->load('O2System\Framework\Services\\' . $serviceClassName);
            }
        }

        // Instantiate Models Container
        if (profiler() !== false) {
            profiler()->watch('Starting Models Container');
        }

        $this->models = new Framework\Containers\Models();

        // Instantiate Cache Service
        if (profiler() !== false) {
            profiler()->watch('Starting Cache Service');
        }
        $this->services->add(new Framework\Services\Cache(config('cache', true)), 'cache');

        // Instantiate Modules Container
        if (profiler() !== false) {
            profiler()->watch('Starting Modules Container');
        }
        $this->modules = new Framework\Containers\Modules();

        if (profiler() !== false) {
            profiler()->watch('Starting O2System Framework Hooks Pre System');
        }
        hooks()->callEvent(Framework\Services\Hooks::PRE_SYSTEM);
    }

    // ------------------------------------------------------------------------

    /**
     * Framework::__reconstruct
     */
    protected function __reconstruct()
    {
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

        if (profiler() !== false) {
            profiler()->watch('Calling Hooks Service: Post System');
        }
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
        $this->services->load(Framework\Cli\Router::class);

        if (profiler() !== false) {
            profiler()->watch('Parse Router Request');
        }
        router()->parseRequest();

        if ($commander = router()->getCommander()) {
            if ($commander instanceof Kernel\Cli\Router\Datastructures\Commander) {
                // Autoload Language
                language()->loadFile($commander->getParameter());
                language()->loadFile($commander->getRequestMethod());
                language()->loadFile($commander->getParameter() . '/' . $commander->getRequestMethod());

                // Autoload Model
                foreach ($this->modules as $module) {
                    if (in_array($module->getType(), ['KERNEL', 'FRAMEWORK'])) {
                        continue;
                    }
                    $module->loadModel();
                }

                // Autoload Model
                $modelClassName = str_replace('Commanders', 'Models', $commander->getName());

                if (class_exists($modelClassName)) {
                    models()->load($modelClassName, 'commander');
                }

                // Initialize Controller
                if (profiler() !== false) {
                    profiler()->watch('Calling Hooks Service: Pre Commander');
                }
                hooks()->callEvent(Framework\Services\Hooks::PRE_COMMANDER);

                if (profiler() !== false) {
                    profiler()->watch('Instantiating Requested Commander: ' . $commander->getClass());
                }
                $requestCommander = $commander->getInstance();

                if (profiler() !== false) {
                    profiler()->watch('Calling Hooks Service: Post Commander');
                }
                hooks()->callEvent(Framework\Services\Hooks::POST_COMMANDER);

                if (profiler() !== false) {
                    profiler()->watch('Execute Requested Commander: ' . $commander->getClass());
                }
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
        // Instantiate Http Router Service
        $this->services->load(Framework\Http\Router::class);

        if (profiler() !== false) {
            profiler()->watch('Parse Router Request');
        }
        router()->parseRequest();

        if (config()->loadFile('session') === true) {

            // Instantiate Session Service
            $session = new Session(config('session', true));
            $session->setLogger($this->services->get('logger'));

            if ( ! $session->isStarted()) {
                $session->start();
            }

            $this->services->add($session, 'session');

            if ($session->has('language') and $this->services->has('language')) {
                language()->setDefault($session->get('language'));
            } else {
                $session->set('language', language()->getDefault());
            }

            if (config('security')->protection[ 'csrf' ] === true) {
                $csrfProtection = new Security\Protections\Csrf();
                $this->services->add($csrfProtection, 'csrfProtection');
            }

            if (config('security')->protection[ 'xss' ] === true) {
                $csrfProtection = new Security\Protections\Xss();
                $this->services->add($xssProtection, 'xssProtection');
            }
        }

        if (config()->loadFile('view') === true) {
            // Instantiate Http UserAgent Service
            $this->services->load(Framework\Http\UserAgent::class);

            // Instantiate Http View Service
            $this->services->load(Framework\Http\Parser::class);

            // Instantiate Http View Service
            $this->services->load(Framework\Http\View::class);

            // Instantiate Http Presenter Service
            $this->services->load(Framework\Http\Presenter::class);
            presenter()->initialize();
        }

        // Instantiate Http Middleware Service
        $this->services->load(Framework\Http\Middleware::class);

        if (profiler() !== false) {
            profiler()->watch('Running Middleware Service: Pre Controller');
        }
        middleware()->run();

        if (false !== ($controller = $this->services->get('controller'))) {
            if ($controller instanceof Kernel\Http\Router\Datastructures\Controller) {
                $controllerParameter = dash($controller->getParameter());
                $controllerRequestMethod = dash($controller->getRequestMethod());

                // Autoload Language
                if ($this->services->has('language')) {
                    language()->loadFile($controller->getParameter());
                    language()->loadFile($controller->getRequestMethod());
                    language()->loadFile($controller->getParameter() . '/' . $controller->getRequestMethod());
                }

                // Autoload Model
                foreach ($this->modules as $module) {
                    if (in_array($module->getType(), ['KERNEL', 'FRAMEWORK'])) {
                        continue;
                    }
                    $module->loadModel();
                }

                // Autoload Model
                $modelClassName = str_replace(['Controllers', 'Presenters'], 'Models', $controller->getName());

                if (class_exists($modelClassName)) {
                    $this->models->register('controller', new $modelClassName());
                }

                // Autoload Assets
                if (config()->loadFile('view') === true) {
                    $controllerAssets = [];
                    $controllerAssetsDirs = [];

                    // Autoload Assets
                    foreach ($this->modules as $module) {
                        if (in_array($module->getType(), ['KERNEL', 'FRAMEWORK'])) {
                            continue;
                        }

                        $controllerAssets[] = $module->getParameter();
                        $controllerAssetsDirs[] = $module->getParameter();
                    }

                    $controllerAssets = array_reverse($controllerAssets);
                    $controllerAssetsDirs = array_reverse($controllerAssetsDirs);

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
                            $this->services->add($presenterClassObject, 'presenter');
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
                if (profiler() !== false) {
                    profiler()->watch('Calling Hooks Service: Pre Controller');
                }

                hooks()->callEvent(Framework\Services\Hooks::PRE_CONTROLLER);

                if (profiler() !== false) {
                    profiler()->watch('Instantiating Requested Controller: ' . $controller->getClass());
                }
                $requestController = $controller->getInstance();

                if (method_exists($requestController, '__reconstruct')) {
                    $requestController->__reconstruct();
                } elseif (method_exists($requestController, 'initialize')) {
                    $requestController->initialize();
                }

                $this->services->add($requestController, 'controller');

                if (profiler() !== false) {
                    profiler()->watch('Calling Middleware Service: Post Controller');
                }
                hooks()->callEvent(Framework\Services\Hooks::POST_CONTROLLER);

                $requestMethod = $controller->getRequestMethod();
                $requestMethodArgs = $controller->getRequestMethodArgs();

                // Call the requested controller method
                if (profiler() !== false) {
                    profiler()->watch('Execute Requested Controller Method');
                }
                ob_start();
                $requestControllerOutput = $requestController->__call($requestMethod, $requestMethodArgs);

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
                } elseif (is_array($requestControllerOutput) or is_object($requestControllerOutput)) {
                    if ($requestController instanceof Framework\Http\Controllers\Restful) {
                        $requestController->sendPayload($requestControllerOutput);
                    } else {
                        output()->send($requestControllerOutput);
                    }
                } elseif (is_numeric($requestControllerOutput)) {
                    output()->sendError($requestControllerOutput);
                }

                if (empty($requestControllerOutput) or $requestControllerOutput === '') {
                    if ($requestController instanceof Framework\Http\Controllers\Restful) {
                        $requestController->sendError(204);
                    } elseif (config()->loadFile('view') === true) {
                        $filenames = [
                            $controllerRequestMethod,
                            $controllerParameter . DIRECTORY_SEPARATOR . $controllerRequestMethod,
                        ];

                        if ($controllerRequestMethod === 'index') {
                            array_unshift($filenames, $controllerParameter);
                        }

                        foreach ($filenames as $filename) {
                            if (view()->getFilePath($filename)) {
                                view()->load($filename);
                            }
                        }

                        if (presenter()->partials->offsetExists('content')) {
                            view()->render();
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
                        view()->render();
                    } else {
                        output()->send($requestControllerOutput);
                    }
                    exit(EXIT_SUCCESS);
                }
            }
        }

        // Show Error (404) Page Not Found
        output()->sendError(404);
    }
}
