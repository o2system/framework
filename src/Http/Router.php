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

namespace O2System\Framework\Http;

// ------------------------------------------------------------------------

use O2System\Framework\Containers\Modules\DataStructures\Module as FrameworkModuleDataStructure;
use O2System\Kernel\Http\Message\Uri as KernelMessageUri;
use O2System\Kernel\Http\Message\Uri\Segments as KernelMessageUriSegments;
use O2System\Kernel\Http\Router as KernelRouter;
use O2System\Kernel\Http\Router\Addresses as KernelAddresses;
use O2System\Kernel\Http\Router\DataStructures\Action as KernelActionDataStructure;
use O2System\Kernel\Http\Router\DataStructures\Controller as KernelControllerDataStructure;
use O2System\Spl\Info\SplFileInfo;

/**
 * Class Router
 *
 * @package O2System
 */
class Router extends KernelRouter
{
    /**
     * Router::handle
     *
     * @param KernelMessageUri|null $uri
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \ReflectionException
     */
    public function handle(KernelMessageUri $uri = null)
    {
        $this->uri = is_null($uri) ? new KernelMessageUri() : $uri;

        // Handle Extension Request
        if ($this->uri->segments->count()) {
            $this->handleExtensionRequest();
        } else {
            $uriPath = urldecode(
                parse_url($_SERVER[ 'REQUEST_URI' ], PHP_URL_PATH)
            );

            $uriPathParts = explode('public/', $uriPath);
            $uriPath = end($uriPathParts);

            if ($uriPath !== '/') {
                $this->uri = $this->uri->withSegments(new KernelMessageUriSegments(
                        array_filter(explode('/', $uriPath)))
                );
            }

            unset($uriPathParts, $uriPath);
        }

        // Load app addresses config
        $this->addresses = config()->loadFile('addresses', true);

        if ($this->addresses instanceof KernelAddresses) {
            // Domain routing
            if (null !== ($domain = $this->addresses->getDomain())) {
                if (is_array($domain)) {
                    $this->uri->segments->exchangeArray(
                        array_merge($domain, $this->uri->segments->getArrayCopy())
                    );
                    $domain = $this->uri->segments->first();
                }

                if (false !== ($app = modules()->getApp($domain))) {
                    $this->registerModule($app);
                } elseif (false !== ($module = modules()->getModule($domain))) {
                    $this->registerModule($module);
                }
            } elseif (false !== ($subdomain = $this->uri->getSubdomain())) {
                if (false !== ($app = modules()->getApp($subdomain))) {
                    $this->registerModule($app);
                }
            }
        }

        // App and Module routing
        if ($numOfUriSegments = $this->uri->segments->count()) {
            if (empty($app)) {
                if (false !== ($module = modules()->getModule($this->uri->segments->first()))) {
                    $this->registerModule($module);

                    if ($module->getType() === 'APP') {
                        $this->uri->segments->shift();
                        $this->handleAppRequest($module);
                    } else {
                        $this->handleSegmentsRequest();
                    }
                }
            } elseif (false !== ($module = modules()->getModule($this->uri->segments->first()))) {
                $this->registerModule($module);

                if ($module->getType() === 'APP') {
                    $this->uri->segments->shift();
                    $this->handleAppRequest($module);
                } else {
                    $this->handleSegmentsRequest();
                }
            } else {
                $this->handleAppRequest($app);
            }
        }

        // Try to translate from uri string
        if (false !== ($action = $this->addresses->getTranslation($this->uri->segments->__toString()))) {
            if ( ! $action->isValidHttpMethod(input()->server('REQUEST_METHOD')) && ! $action->isAnyHttpMethod()) {
                output()->sendError(405);
            } else {
                // Checks if action closure is an array
                if (is_array($closureSegments = $action->getClosure())) {
                    $this->uri->segments->exchangeArray($closureSegments);

                    if (false !== ($module = modules()->getModule($this->uri->segments->first()))) {
                        $this->registerModule($module);

                        if ($module->getType() === 'APP') {
                            $this->uri->segments->shift();
                            $this->handleAppRequest($module);
                        } else {
                            $this->handleSegmentsRequest();
                        }
                    } else {
                        $this->handleSegmentsRequest();
                    }
                } else {
                    if (false !== ($parseSegments = $action->getParseUriString($this->uri->segments->__toString()))) {
                        $uriSegments = $parseSegments;
                    } else {
                        $uriSegments = [];
                    }

                    $this->uri = $this->uri->withSegments(new KernelMessageUriSegments($uriSegments));
                    $uriString = $this->uri->segments->__toString();

                    $this->parseAction($action, $uriSegments);
                    if ( ! empty(services()->has('controller'))) {
                        return true;
                    }
                }
            }
        }

        // Try to get route from controller & page
        if ($numOfUriSegments = $this->uri->segments->count()) {
            $uriSegments = $this->uri->segments->getArrayCopy();
            $uriString = $this->uri->segments->__toString();

            for ($i = 0; $i <= $numOfUriSegments; $i++) {
                $uriRoutedSegments = array_slice($uriSegments, 0, ($numOfUriSegments - $i));
                $modules = modules()->getArrayCopy();

                foreach ($modules as $module) {
                    $controllerNamespace = $module->getNamespace() . 'Controllers\\';

                    if ($module->getNamespace() === 'O2System\Framework\\') {
                        $controllerNamespace = 'O2System\Framework\Http\Controllers\\';
                    }

                    /**
                     * Try to find requested controller
                     */
                    if (class_exists($controllerClassName = $controllerNamespace . implode('\\',
                            array_map('studlycase', $uriRoutedSegments)))) {

                        if ($controllerClassName::$inherited) {
                            $uriSegments = array_diff($uriSegments, $uriRoutedSegments);
                            $this->setController(new KernelControllerDataStructure($controllerClassName),
                                $uriSegments);

                            break;
                        } else {
                            $uriSegments = array_diff($uriSegments, $uriRoutedSegments);
                            $this->setController(new KernelControllerDataStructure($controllerClassName),
                                $uriSegments);

                            break;
                        }
                    }

                    /**
                     * Try to find requested page
                     */
                    if (false !== ($pagesDir = $module->getResourcesDir('pages'))) {
                        if ($controllerClassName = $this->getPagesControllerClassName()) {

                            /**
                             * Try to find from database
                             */
                            $modelClassName = str_replace('Controllers', 'Models', $controllerClassName);

                            if (class_exists($modelClassName)) {
                                models()->load($modelClassName, 'controller');

                                if (false !== ($page = models('controller')->find($uriString, 'segments'))) {
                                    if (isset($page->content)) {
                                        presenter()->partials->offsetSet('content', $page->content);

                                        $this->setController(
                                            (new KernelControllerDataStructure($controllerClassName))
                                                ->setRequestMethod('index')
                                        );

                                        return true;
                                        break;
                                    }
                                }
                            }

                            /**
                             * Try to find from page file
                             */
                            foreach (['.phtml', '.vue'] as $pageExtension) {
                                $pageFilePath = $pagesDir . implode(DIRECTORY_SEPARATOR,
                                        array_map('dash', $uriRoutedSegments)) . $pageExtension;

                                if (is_file($pageFilePath)) {
                                    presenter()->page->setFile($pageFilePath);
                                    break;
                                } else {
                                    $pageFilePath = str_replace($pageExtension,
                                        DIRECTORY_SEPARATOR . 'index' . $pageExtension, $pageFilePath);
                                    if (is_file($pageFilePath)) {
                                        presenter()->page->setFile($pageFilePath);
                                        break;
                                    }
                                }
                            }

                            if (presenter()->page->file instanceof SplFileInfo) {
                                $this->setController(
                                    (new KernelControllerDataStructure($controllerClassName))
                                        ->setRequestMethod('index')
                                );

                                return true;
                                break;
                            }
                        }
                    }
                }

                // break the loop if the controller has been set
                if (services()->has('controller')) {
                    return true;
                    break;
                }
            }
        }

        if (class_exists($controllerClassName = modules()->top()->getDefaultControllerClassName())) {
            $this->setController(new KernelControllerDataStructure($controllerClassName),
                $this->uri->segments->getArrayCopy());

            return true;
        }

        // Let's the framework do the rest when there is no controller found
        // the framework will redirect to PAGE 404
    }

    // ------------------------------------------------------------------------

    /**
     * Router::handleExtensionRequest
     */
    protected function handleExtensionRequest()
    {
        $lastSegment = $this->uri->segments->last();

        if (strpos($lastSegment, '.json') !== false) {
            output()->setContentType('application/json');
            $lastSegment = str_replace('.json', '', $lastSegment);
            $this->uri->segments->pop();
            $this->uri->segments->push($lastSegment);
        } elseif (strpos($lastSegment, '.xml') !== false) {
            output()->setContentType('application/xml');
            $lastSegment = str_replace('.xml', '', $lastSegment);
            $this->uri->segments->pop();
            $this->uri->segments->push($lastSegment);
        } elseif (strpos($lastSegment, '.js') !== false) {
            output()->setContentType('application/x-javascript');
            $lastSegment = str_replace('.js', '', $lastSegment);
            $this->uri->segments->pop();
            $this->uri->segments->push($lastSegment);
        } elseif (strpos($lastSegment, '.css') !== false) {
            output()->setContentType('text/css');
            $lastSegment = str_replace('.css', '', $lastSegment);
            $this->uri->segments->pop();
            $this->uri->segments->push($lastSegment);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Router::handleAppRequest
     *
     * @param \O2System\Framework\Containers\Modules\DataStructures\Module $app
     */
    public function handleAppRequest(FrameworkModuleDataStructure $app)
    {
        // Find App module
        foreach([null,'modules', 'plugins'] as $additionalSegment) {
            if(empty($additionalSegment)) {
                $segments = [
                    $app->getParameter(),
                    $this->uri->segments->first(),
                ];
            } else {
                $segments = [
                    $app->getParameter(),
                    $additionalSegment,
                    $this->uri->segments->first(),
                ];
            }

            if (false !== ($module = modules()->getModule($segments))) {
                $this->uri->segments->shift();

                $this->registerModule($module);
                $this->handleSegmentsRequest();
                break;
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Router::handleModuleRequest
     */
    public function handleSegmentsRequest()
    {
        $module = modules()->getActiveModule();

        if ($numOfUriSegments = $this->uri->segments->count()) {
            $uriSegments = $this->uri->segments->getArrayCopy();

            for ($i = 0; $i <= $numOfUriSegments; $i++) {
                $uriRoutedSegments = array_diff($uriSegments,
                    array_slice($uriSegments, ($numOfUriSegments - $i)));

                if(count($uriRoutedSegments)) {
                    if($module instanceof FrameworkModuleDataStructure) {
                        $moduleSegments = $module->getSegments();

                        if(count($moduleSegments)) {
                            $uriRoutedSegments = array_merge($moduleSegments, $uriRoutedSegments);
                        }
                    }

                    if (false !== ($module = modules()->getModule($uriRoutedSegments))) {
                        $uriSegments = array_diff($uriSegments, $uriRoutedSegments);
                        $this->uri->segments->exchangeArray($uriSegments);

                        $this->registerModule($module);
                        $this->handleSegmentsRequest();
                        break;
                    }
                }
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Router::getPagesControllerClassName
     *
     * @return bool|string
     */
    final protected function getPagesControllerClassName()
    {
        $modules = modules()->getArrayCopy();

        foreach ($modules as $module) {
            $controllerClassName = $module->getNamespace() . 'Controllers\Pages';
            if ($module->getNamespace() === 'O2System\Framework\\') {
                $controllerClassName = 'O2System\Framework\Http\Controllers\Pages';
            }

            if (class_exists($controllerClassName)) {
                return $controllerClassName;
                break;
            }
        }

        if (class_exists('O2System\Framework\Http\Controllers\Pages')) {
            return 'O2System\Framework\Http\Controllers\Pages';
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Router::registerModule
     *
     * @param FrameworkModuleDataStructure $module
     */
    final public function registerModule(FrameworkModuleDataStructure $module)
    {
        // Push Subdomain App Module
        modules()->push($module);

        // Add Config FilePath
        config()->addFilePath($module->getRealPath());

        // Reload Config
        config()->reload();

        // Load modular addresses config
        if (false !== ($configDir = $module->getDir('config', true))) {
            unset($addresses);

            $reconfig = false;
            if (is_file(
                $filePath = $configDir . ucfirst(
                        strtolower(ENVIRONMENT)
                    ) . DIRECTORY_SEPARATOR . 'Addresses.php'
            )) {
                require($filePath);
                $reconfig = true;
            } elseif (is_file(
                $filePath = $configDir . 'Addresses.php'
            )) {
                require($filePath);
                $reconfig = true;
            }

            if ( ! $reconfig) {
                $controllerNamespace = $module->getNamespace() . 'Controllers\\';
                $controllerClassName = $controllerNamespace . studlycase($module->getParameter());

                if (class_exists($controllerClassName)) {
                    $this->addresses->any(
                        '/',
                        function () use ($controllerClassName) {
                            return new $controllerClassName();
                        }
                    );
                }
            } elseif (isset($addresses)) {
                $this->addresses = $addresses;
            }
        } else {
            $controllerNamespace = $module->getNamespace() . 'Controllers\\';
            $controllerClassName = $controllerNamespace . studlycase($module->getParameter());

            if (class_exists($controllerClassName)) {
                $this->addresses->any(
                    '/',
                    function () use ($controllerClassName) {
                        return new $controllerClassName();
                    }
                );
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Router::parseAction
     *
     * @param KernelActionDataStructure $action
     * @param array                     $uriSegments
     *
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \ReflectionException
     */
    protected function parseAction(KernelActionDataStructure $action, array $uriSegments = [])
    {
        ob_start();
        $closure = $action->getClosure();
        if (empty($closure)) {
            $closure = ob_get_contents();
        }
        ob_end_clean();

        if ($closure instanceof Controller) {
            $uriSegments = empty($uriSegments)
                ? $action->getClosureParameters()
                : $uriSegments;
            $this->setController(
                (new KernelControllerDataStructure($closure))
                    ->setRequestMethod('index'),
                $uriSegments
            );
        } elseif ($closure instanceof KernelControllerDataStructure) {
            $this->setController($closure, $action->getClosureParameters());
        } elseif (is_array($closure)) {
            $this->uri = (new KernelMessageUri())
                ->withSegments(new KernelMessageUriSegments(''))
                ->withQuery('');
            $this->handle($this->uri->addSegments($closure));
        } else {
            if (class_exists($closure)) {
                $this->setController(
                    (new KernelControllerDataStructure($closure))
                        ->setRequestMethod('index'),
                    $uriSegments
                );
            } elseif (preg_match("/([a-zA-Z0-9\\\]+)(@)([a-zA-Z0-9\\\]+)/", $closure, $matches)) {
                $this->setController(
                    (new KernelControllerDataStructure($matches[ 1 ]))
                        ->setRequestMethod($matches[ 3 ]),
                    $uriSegments
                );
            } elseif ($theme = presenter()->theme) {
                if($theme->use === true) {
                    if ( ! presenter()->partials->offsetExists('content') && $closure !== '') {
                        presenter()->partials->offsetSet('content', $closure);
                    }

                    if (presenter()->partials->offsetExists('content')) {
                        profiler()->watch('VIEW_SERVICE_RENDER');
                        view()->render();
                        exit(EXIT_SUCCESS);
                    } else {
                        output()->sendError(204);
                        exit(EXIT_ERROR);
                    }
                }
            } elseif (is_string($closure) && $closure !== '') {
                if (is_json($closure)) {
                    output()->setContentType('application/json');
                    output()->send($closure);
                } else {
                    output()->send($closure);
                }
            } elseif (is_array($closure) || is_object($closure)) {
                output()->send($closure);
            } elseif (is_numeric($closure)) {
                output()->sendError($closure);
            } else {
                output()->sendError(204);
                exit(EXIT_ERROR);
            }
        }
    }
}