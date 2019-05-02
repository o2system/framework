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
     * Router::parseRequest
     *
     * @param KernelMessageUri|null $uri
     *
     * @return bool
     * @throws \ReflectionException
     */
    public function parseRequest(KernelMessageUri $uri = null)
    {
        $this->uri = is_null($uri) ? new KernelMessageUri() : $uri;
        $uriSegments = $this->uri->getSegments()->getParts();
        $uriString = $this->uri->getSegments()->getString();

        if ($this->uri->getSegments()->getTotalParts()) {
            if (strpos(end($uriSegments), '.json') !== false) {
                output()->setContentType('application/json');
                $endSegment = str_replace('.json', '', end($uriSegments));
                array_pop($uriSegments);
                array_push($uriSegments, $endSegment);
                $this->uri = $this->uri->withSegments(new KernelMessageUriSegments($uriSegments));
                $uriString = $this->uri->getSegments()->getString();
            } elseif (strpos(end($uriSegments), '.xml') !== false) {
                output()->setContentType('application/xml');
                $endSegment = str_replace('.xml', '', end($uriSegments));
                array_pop($uriSegments);
                array_push($uriSegments, $endSegment);
                $this->uri = $this->uri->withSegments(new KernelMessageUriSegments($uriSegments));
                $uriString = $this->uri->getSegments()->getString();
            } elseif (strpos(end($uriSegments), '.js') !== false) {
                output()->setContentType('application/x-javascript');
                $endSegment = str_replace('.js', '', end($uriSegments));
                array_pop($uriSegments);
                array_push($uriSegments, $endSegment);
                $this->uri = $this->uri->withSegments(new KernelMessageUriSegments($uriSegments));
                $uriString = $this->uri->getSegments()->getString();
            } elseif (strpos(end($uriSegments), '.css') !== false) {
                output()->setContentType('text/css');
                $endSegment = str_replace('.css', '', end($uriSegments));
                array_pop($uriSegments);
                array_push($uriSegments, $endSegment);
                $this->uri = $this->uri->withSegments(new KernelMessageUriSegments($uriSegments));
                $uriString = $this->uri->getSegments()->getString();
            }
        } else {
            $uriPath = urldecode(
                parse_url($_SERVER[ 'REQUEST_URI' ], PHP_URL_PATH)
            );

            $uriPathParts = explode('public/', $uriPath);
            $uriPath = end($uriPathParts);

            if ($uriPath !== '/') {
                $uriString = $uriPath;
                $uriSegments = array_filter(explode('/', $uriString));

                $this->uri = $this->uri->withSegments(new KernelMessageUriSegments($uriSegments));
                $uriString = $this->uri->getSegments()->getString();
            }
        }

        // Load app addresses config
        $this->addresses = config()->loadFile('addresses', true);

        if ($this->addresses instanceof KernelAddresses) {
            // Domain routing
            if (null !== ($domain = $this->addresses->getDomain())) {
                if (is_array($domain)) {
                    $uriSegments = array_merge($domain, $uriSegments);
                    $this->uri = $this->uri->withSegments(new KernelMessageUriSegments($uriSegments));
                    $uriString = $this->uri->getSegments()->getString();
                    $domain = reset($uriSegments);
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

        // Module routing
        if ($numOfUriSegments = count($uriSegments)) {
            if (empty($app)) {
                if (false !== ($module = modules()->getModule( reset($uriSegments) ))) {
                    //array_shift($uriSegments);
                    $this->uri = $this->uri->withSegments(new KernelMessageUriSegments($uriSegments));
                    $uriString = $this->uri->getSegments()->getString();

                    $this->registerModule($module);
                }
            }

            if($numOfUriSegments = count($uriSegments)) {
                for ($i = 0; $i <= $numOfUriSegments; $i++) {
                    $uriRoutedSegments = array_diff($uriSegments,
                        array_slice($uriSegments, ($numOfUriSegments - $i)));

                    if (false !== ($module = modules()->getModule($uriRoutedSegments))) {
                        $uriSegments = array_diff($uriSegments, $uriRoutedSegments);
                        $this->uri = $this->uri->withSegments(new KernelMessageUriSegments($uriSegments));
                        $uriString = $this->uri->getSegments()->getString();

                        $this->registerModule($module);

                        break;
                    }
                }
            }
        }

        // Try to translate from uri string
        if (false !== ($action = $this->addresses->getTranslation($uriString))) {
            if ( ! $action->isValidHttpMethod(input()->server('REQUEST_METHOD')) && ! $action->isAnyHttpMethod()) {
                output()->sendError(405);
            } else {
                // Checks if action closure is an array
                if (is_array($closureSegments = $action->getClosure())) {
                    // Closure App Routing
                    if (false !== ($app = modules()->getModule(reset($closureSegments)))) {
                        array_shift($closureSegments);
                        $this->registerModule($app);
                    }

                    // Closure Module routing
                    if ($numOfClosureSegments = count($closureSegments)) {
                        for ($i = 0; $i <= $numOfClosureSegments; $i++) {
                            $closureRoutedSegments = array_diff($closureSegments,
                                array_slice($closureSegments, ($numOfClosureSegments - $i)));

                            if ( ! empty($app)) {
                                if (reset($closureSegments) !== $app->getParameter()) {
                                    array_unshift($closureRoutedSegments, $app->getParameter());
                                }
                            }

                            if (false !== ($module = modules()->getModule($closureRoutedSegments))) {
                                $uriSegments = array_diff($closureSegments, $closureRoutedSegments);
                                $this->uri = $this->uri->withSegments(new KernelMessageUriSegments($closureSegments));
                                $uriString = $this->uri->getSegments()->getString();

                                $this->registerModule($module);

                                break;
                            }
                        }
                    }
                } else {
                    if (false !== ($parseSegments = $action->getParseUriString($uriString))) {
                        $uriSegments = $parseSegments;
                    } else {
                        $uriSegments = [];
                    }

                    $this->uri = $this->uri->withSegments(new KernelMessageUriSegments($uriSegments));
                    $uriString = $this->uri->getSegments()->getString();

                    $this->parseAction($action, $uriSegments);
                    if ( ! empty(services()->has('controller'))) {
                        return true;
                    }
                }
            }
        }

        // Try to get route from controller & page
        if ($numOfUriSegments = count($uriSegments)) {
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

                        if($controllerClassName::$inherited) {
                            $uriSegments = array_diff($uriSegments, $uriRoutedSegments);
                            $this->setController(new KernelControllerDataStructure($controllerClassName),
                                $uriSegments);

                            break;
                        }
                    }

                    /**
                     * Try to find requested page
                     */
                    if (false !== ($pagesDir = $module->getResourcesDir('pages', true))) {
                        if($controllerClassName = $this->getPagesControllerClassName()) {

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
                            $pageFilePath = $pagesDir . implode(DIRECTORY_SEPARATOR,
                                    array_map('dash', $uriRoutedSegments)) . '.phtml';

                            if (is_file($pageFilePath)) {
                                presenter()->page->setFile($pageFilePath);
                            } else {
                                $pageFilePath = str_replace('.phtml', DIRECTORY_SEPARATOR . 'index.phtml', $pageFilePath);
                                if(is_file($pageFilePath)) {
                                    presenter()->page->setFile($pageFilePath);
                                }
                            }

                            if(presenter()->page->file instanceof SplFileInfo) {
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
                $uriSegments);

            return true;
        }

        // Let's the framework do the rest when there is no controller found
        // the framework will redirect to PAGE 404
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

        foreach($modules as $module) {
            $controllerClassName = $module->getNamespace() . 'Controllers\Pages';
            if ($module->getNamespace() === 'O2System\Framework\\') {
                $controllerClassName = 'O2System\Framework\Http\Controllers\Pages';
            }

            if(class_exists($controllerClassName)) {
                return $controllerClassName;
                break;
            }
        }

        if(class_exists('O2System\Framework\Http\Controllers\Pages')) {
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
            $this->parseRequest($this->uri->addSegments($closure));
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
            } elseif (presenter()->theme->use === true) {
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