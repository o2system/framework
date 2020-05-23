<?php
/**
 * This file is part of the O2System Framework package.
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

use O2System\Kernel\Http\Message\Uri;

switch (strtoupper(ENVIRONMENT)) {
    case 'DEVELOPMENT':
        ini_set('display_errors', 1);
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        break;
    case 'TESTING':
    case 'PRODUCTION':
        ini_set('display_errors', 0);
        error_reporting(-1);
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
 * DATABASE PATH
 *---------------------------------------------------------------
 *
 * RealPath to writable database folder.
 *
 * WITH TRAILING SLASH!
 */
if ( ! defined('PATH_DATABASE')) {
    define('PATH_DATABASE', PATH_ROOT . DIR_DATABASE . DIRECTORY_SEPARATOR);
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
require __DIR__ . '/Helpers/Array.php';
require __DIR__ . '/Helpers/Object.php';
require __DIR__ . '/Helpers/Framework.php';
require __DIR__ . '/Helpers/Datetime.php';

/**
 * Class Framework
 *
 * @package O2System
 */
class Framework extends Kernel
{
    /**
     * Framework::$config
     *
     * Framework Container Config
     *
     * @var Framework\Containers\Config
     */
    public $config;

    /**
     * Framework::$globals
     *
     * Framework Container Globals
     *
     * @var Framework\Containers\Globals
     */
    public $globals;

    /**
     * Framework::$environment
     *
     * Framework Container Environment
     *
     * @var Framework\Containers\Environment
     */
    public $environment;

    /**
     * Framework::$models
     *
     * Framework Container Models
     *
     * @var Framework\Containers\Models
     */
    public $models;

    /**
     * Framework::$modules
     *
     * Framework Container Modules
     *
     * @var Framework\Containers\Modules
     */
    public $modules;

    // ------------------------------------------------------------------------

    /**
     * Framework::__construct
     */
    protected function __construct()
    {
        parent::__construct();

        if (profiler() !== false) {
            profiler()->watch('Starting O2System Framework Hooks Pre System');
        }

        $this->services->load(Framework\Services\Hooks::class, 'hooks');

        hooks()->callEvent(Framework\Services\Hooks::PRE_SYSTEM);

        if (profiler() !== false) {
            profiler()->watch('Starting O2System Framework');
        }

        // Add App Views Folder
        output()->addFilePath(PATH_APP);

        if (profiler() !== false) {
            profiler()->watch('Starting Framework Services');
        }

        // Instantiate Config Container
        if (profiler() !== false) {
            profiler()->watch('Starting Config Container');
        }
        $this->config = new Framework\Containers\Config();

        // Set Default Language
        if($languageDefault = $this->config['language']['default']) {
            language()->setDefault($languageDefault);
            unset($languageDefault);
        }

        // Set Language Options
        if($languageOptions = $this->config['language']['options']) {
            language()->setOptions($languageOptions);
            unset($languageOptions);
        }

        // Instantiate Shutdown Service Container
        $this->services->load('Services\Shutdown', 'shutdown');

        // Instantiate Logger Service
        if($this->config[ 'logger' ][ 'threshold' ] !== LOGGER_DISABLED) {
            $this->services->load('Services\Logger', 'logger');
        }

        // Instantiate Loader Service
        $this->services->load('Services\Loader', 'loader');

        // Instantiate Globals Container
        if (profiler() !== false) {
            profiler()->watch('Starting Globals Container');
        }
        $this->globals = new Kernel\DataStructures\Input\Globals();

        // Instantiate Environment Container
        if (profiler() !== false) {
            profiler()->watch('Starting Environment Container');
        }
        $this->environment = new Kernel\DataStructures\Input\Env();

        // Instantiate Models Container
        if (profiler() !== false) {
            profiler()->watch('Starting Models Container');
        }
        $this->models = new Framework\Containers\Models();

        if (config()->loadFile('cache') === true) {
            // Instantiate Cache Service
            if (profiler() !== false) {
                profiler()->watch('Starting Cache Service');
            }

            $this->services->add(new Framework\Services\Cache(config('cache', true)), 'cache');
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Framework::__reconstruct
     */
    protected function __reconstruct()
    {
        if (profiler() !== false) {
            profiler()->watch('Calling Hooks Service: Post System');
        }
        hooks()->callEvent(Framework\Services\Hooks::POST_SYSTEM);

        if (is_cli()) {
            // Instantiate CLI Router Service
            $this->services->load(Kernel\Cli\Router::class);

            $this->cliHandler();
        } else {
            // Instantiate Http Router Service
            $this->services->load(Framework\Http\Router::class);

            // Instantiate Modules Container
            if (profiler() !== false) {
                profiler()->watch('Starting Modules Container');
            }
            $this->modules = new Framework\Containers\Modules();

            $this->httpHandler();
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Framework::cliHandler
     *
     * @return void
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \ReflectionException
     */
    private function cliHandler()
    {
        if (profiler() !== false) {
            profiler()->watch('Parse Router Request');
        }
        router()->handle();

        if ($commander = router()->getCommander()) {
            if ($commander instanceof Kernel\Cli\Router\DataStructures\Commander) {
                // Autoload Language
                language()->loadFile($commander->getParameter());
                language()->loadFile($commander->getRequestMethod());
                language()->loadFile($commander->getParameter() . '/' . $commander->getRequestMethod());

                $modules = $this->modules->getArrayCopy();

                // Run Module Autoloader
                foreach ($modules as $module) {
                    if (in_array($module->getType(), ['KERNEL', 'FRAMEWORK'])) {
                        continue;
                    }
                    $module->loadModel();
                }

                // Autoload Model
                $modelClassName = str_replace('Commanders', 'Models', $commander->getName());

                if (class_exists($modelClassName)) {
                    $this->models->load($modelClassName, 'commander');
                }

                // Initialize Commander
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

                $requestCommander->__call($commander->getRequestMethod());

                exit(EXIT_SUCCESS);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Framework::httpHandler
     *
     * @return void
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \ReflectionException
     */
    private function httpHandler()
    {
        if (config()->loadFile('view') === true) {
            // Instantiate Http UserAgent Service
            $this->services->load(Framework\Http\UserAgent::class, 'userAgent');

            // Instantiate Http View Service
            $this->services->load(Framework\Http\Parser::class);

            // Instantiate Http View Service
            $this->services->load(Framework\Http\View::class);

            // Instantiate Http Presenter Service
            $this->services->load(Framework\Http\Presenter::class);

            // Initialize Http Presenter Service
            if (profiler() !== false) {
                profiler()->watch('Initialize Http Presenter Service');
            }
        }

        // Initialize Http Presenter Service
        if (profiler() !== false) {
            profiler()->watch('Registering App');
        }

        if(empty($this->config->get('app'))) {
            $app = (new Framework\Containers\Modules\DataStructures\Module(PATH_APP))
                ->setType('APP')
                ->setNamespace('App\\');
            $this->modules->register($app);
        } else {
            $app = (new Framework\Containers\Modules\DataStructures\Module(PATH_APP .  $this->config->get('app') . DIRECTORY_SEPARATOR))
                ->setType('APP')
                ->setNamespace('App\\' . studlycase($this->config->get('app')) . '\\');
            $this->modules->register($app);
        }
        
        if (profiler() !== false) {
            profiler()->watch('Parse Router Request');
        }

        router()->handle();

        if (config()->loadFile('session') === true) {

            // Instantiate Session Service
            $session = new Session(config('session', true));
            $session->setLogger($this->services->get('logger'));

            if ( ! $session->isStarted()) {
                $firstSegment = server_request()->getUri()->segments->first();
                if( ! in_array($firstSegment, ['resources', 'images', 'storage'])) {
                    $session->start();
                }
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
                $xssProtection = new Security\Protections\Xss();
                $this->services->add($xssProtection, 'xssProtection');
            }
        }

        // Instantiate Http Middleware Service
        $this->services->load(Framework\Http\Middleware::class);

        if (profiler() !== false) {
            profiler()->watch('Running Middleware Service: Pre Controller');
        }
        middleware()->run();

        if ($this->services->has('controller')) {
            $controller = $this->services->get('controller');

            $controllerParameter = dash($controller->getParameter());
            $controllerRequestMethod = dash($controller->getRequestMethod());

            // Controller Autoload Language
            if ($this->services->has('language')) {
                language()->loadFile($controller->getParameter());
                language()->loadFile($controller->getRequestMethod());
                language()->loadFile($controller->getParameter() . '/' . $controller->getRequestMethod());
            }

            // Controller Autoload Model
            $modelClassName = str_replace(['Controllers', 'Presenters'], 'Models', $controller->getName());

            if (class_exists($modelClassName)) {
                $this->models->load($modelClassName, 'controller');
            }

            if ($this->services->has('view')) {
                // Autoload Presenter
                $presenterClassName = str_replace('Controllers', 'Presenters', $controller->getName());

                if (class_exists($presenterClassName)) {
                    $presenterClassObject = new $presenterClassName();
                    if ($presenterClassObject instanceof Framework\Http\Presenter) {
                        $this->services->add($presenterClassObject, 'presenter');
                    }
                }
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
            }

            if (profiler() !== false) {
                profiler()->watch('Calling Hooks Service: Post Controller');
            }
            hooks()->callEvent(Framework\Services\Hooks::POST_CONTROLLER);

            if (profiler() !== false) {
                profiler()->watch('Calling Middleware Service: Post Controller');
            }
            middleware()->run();

            $requestMethod = $controller->getRequestMethod();
            $requestMethodArgs = $controller->getRequestMethodArgs();

            // Call the requested controller method
            if (profiler() !== false) {
                profiler()->watch('Execute Requested Controller Method');
            }

            ob_start();
            $requestControllerOutput = $requestController->__call($requestMethod, $requestMethodArgs);

            if(empty($requestControllerOutput)) {
                $requestControllerOutput = ob_get_contents();
            }

            ob_end_clean();

            if (is_numeric($requestControllerOutput)) {
                output()->sendError($requestControllerOutput);
            } elseif (is_bool($requestControllerOutput)) {
                if ($requestControllerOutput === true) {
                    output()->sendError(200);
                } elseif ($requestControllerOutput === false) {
                    output()->sendError(204);
                }
            } elseif (is_array($requestControllerOutput) or is_object($requestControllerOutput)) {
                output()->setContentType('application/json');
                output()->sendPayload($requestControllerOutput);
            } elseif ($requestController instanceof Framework\Http\Controllers\Restful) {
                if (empty($requestControllerOutput)) {
                    output()->sendError(204);
                } elseif (is_string($requestControllerOutput)) {
                    if (is_json($requestControllerOutput)) {
                        output()->setContentType('application/json');
                    } else {
                        output()->setContentType('text/plain');
                    }

                    echo $requestControllerOutput;
                }
            } elseif (is_string($requestControllerOutput)) {
                if(is_json($requestControllerOutput)) {
                    output()->setContentType('application/json');
                    output()->send($requestControllerOutput);
                } elseif(is_ajax()) {
                    output()->setContentType('application/json');
                    if(empty($requestControllerOutput) or $requestControllerOutput === '') {
                        output()->sendError(204);
                    } else {
                        output()->sendPayload($requestControllerOutput);
                    }
                } elseif ($this->services->has('view')) {
                    if (empty($requestControllerOutput) or $requestControllerOutput === '') {
                        if(presenter()->partials->offsetExists('content')) {
                            $htmlOutput = view()->render();

                            if (empty($htmlOutput)) {
                                output()->sendError(204);
                            } else {
                                output()->setContentType('text/html');
                                output()->send($htmlOutput);
                            }
                        } else {
                            output()->sendError(204);
                        }
                    } else {
                        presenter()->partials->offsetSet('content', $requestControllerOutput);

                        $htmlOutput = view()->render();

                        if (empty($htmlOutput)) {
                            output()->sendError(204);
                        } else {
                            output()->setContentType('text/html');
                            output()->send($htmlOutput);
                        }
                    }
                }
            }
        } else {
            // Show Error (404) Page Not Found
            output()->sendError(404);
        }
    }
}
