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

use O2System\Kernel;
use O2System\Framework;

/**
 * Class Router
 *
 * @package O2System
 */
class Router extends Kernel\Http\Router
{
    public function parseRequest(Kernel\Http\Message\Uri $uri = null)
    {
        $uri = is_null($uri) ? server_request()->getUri() : $uri;
        $uriSegments = $uri->getSegments()->getParts();
        $uriString = $uri->getSegments()->getString();

        if (empty($uriSegments)) {
            $uriPath = urldecode(
                parse_url($_SERVER[ 'REQUEST_URI' ], PHP_URL_PATH)
            );

            $uriPathParts = explode('public/', $uriPath);
            $uriPath = end($uriPathParts);

            if ($uriPath !== '/') {
                $uriString = $uriPath;
                $uriSegments = array_filter(explode('/', $uriString));
            }
        }

        // Load app addresses config
        $this->addresses = config()->loadFile('addresses', true);

        if ($this->addresses instanceof Kernel\Http\Router\Addresses) {
            // Domain routing
            if (null !== ($domain = $this->addresses->getDomain())) {
                if (is_array($domain)) {
                    $uriSegments = array_merge($domain, $uriSegments);
                    $uriString = implode('/', array_map('dash', $uriSegments));
                    $domain = reset($uriSegments);
                }

                if (false !== ($app = modules()->getApp($domain))) {
                    $this->registerModule($app);
                } elseif (false !== ($module = modules()->getModule($domain))) {
                    $this->registerModule($module);
                }
            } elseif (false !== ($subdomain = $uri->getSubdomain())) {
                if (false !== ($module = modules()->getApp($subdomain))) {
                    $this->registerModule($module);
                }
            }
        }

        // Module routing
        if ($uriTotalSegments = count($uriSegments)) {
            for ($i = 0; $i <= $uriTotalSegments; $i++) {
                $uriRoutedSegments = array_diff($uriSegments,
                    array_slice($uriSegments, ($uriTotalSegments - $i)));

                if (false !== ($module = modules()->getModule($uriRoutedSegments))) {
                    $uriSegments = array_diff($uriSegments, $uriRoutedSegments);
                    if(empty($uriSegments)) {
                        $uriSegments = [];
                        $uriString = '/';
                    } else {
                        $uriString = implode('/', $uriSegments);
                    }
                    
                    $this->registerModule($module);

                    break;
                }
            }
        }

        // Try to translate from uri string
        if (false !== ($action = $this->addresses->getTranslation($uriString))) {
            if ( ! $action->isValidHttpMethod(server_request()->getMethod()) && ! $action->isAnyHttpMethod()) {
                output()->sendError(405);
            } else {
                if (false !== ($parseSegments = $action->getParseUriString($uriString))) {
                    $uriSegments = $parseSegments;
                } else {
                    $uriSegments = [];
                }

                $this->parseAction($action, $uriSegments);
                if ( ! empty(o2system()->hasService('controller'))) {
                    return;
                }
            }
        }

        // Try to get route from controller & page
        if ($uriTotalSegments = count($uriSegments)) {
            for ($i = 0; $i <= $uriTotalSegments; $i++) {
                $uriRoutedSegments = array_slice($uriSegments, 0, ($uriTotalSegments - $i));

                $modules = modules()->getArrayCopy();

                foreach ($modules as $module) {

                    $controllerNamespace = $module->getNamespace() . 'Controllers\\';
                    if ($module->getNamespace() === 'O2System\Framework\\') {
                        $controllerNamespace = 'O2System\Framework\Http\Controllers\\';
                    }

                    $controllerClassName = $controllerNamespace . implode('\\',
                            array_map('studlycase', $uriRoutedSegments));

                    if (class_exists($controllerClassName)) {
                        $uriSegments = array_diff($uriSegments, $uriRoutedSegments);
                        $this->setController(new Kernel\Http\Router\Datastructures\Controller($controllerClassName),
                            $uriSegments);

                        break;
                    } elseif(class_exists($module->getDefaultControllerClassName())) {
                        $this->setController(new Kernel\Http\Router\Datastructures\Controller($module->getDefaultControllerClassName()),
                            $uriSegments);
                    } elseif (false !== ($pagesDir = $module->getDir('pages', true))) {
                        $pageFilePath = $pagesDir . implode(DIRECTORY_SEPARATOR,
                                array_map('studlycase', $uriRoutedSegments)) . '.phtml';

                        if (is_file($pageFilePath)) {
                            if ($this->setPage(new Framework\Http\Router\Datastructures\Page($pageFilePath)) !== false) {
                                return;

                                break;
                            }
                        }
                    } elseif (class_exists($controllerClassName = $controllerNamespace . 'Pages')) {
                        $modelClassName = str_replace('Controllers', 'Models', $controllerClassName);

                        if (class_exists($modelClassName)) {
                            models()->register('controller', new $modelClassName);

                            if (false !== ($page = models()->controller->find($uriString, 'segments'))) {
                                $controller = new $controllerClassName();

                                if (method_exists($controller, 'setPage')) {
                                    $controller->setPage($page);

                                    $this->setController(
                                        (new Kernel\Http\Router\Datastructures\Controller($controller))
                                            ->setRequestMethod('index')
                                    );

                                    return true;
                                }
                            }
                        }
                    }
                }

                // break the loop if the controller has been set
                if ( ! empty(o2system()->hasService('controller'))) {
                    break;
                }
            }
        }

        // Let's the framework do the rest when there is no controller found
        // the framework will redirect to PAGE 404
    }

    // ------------------------------------------------------------------------

    final protected function registerModule(Framework\Datastructures\Module $module)
    {
        // Push Subdomain App Module
        modules()->push($module);

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

    protected function parseAction(Kernel\Http\Router\Datastructures\Action $action, array $uriSegments = [])
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
                (new Kernel\Http\Router\Datastructures\Controller($closure))
                    ->setRequestMethod('index'),
                $uriSegments
            );
        } elseif ($closure instanceof Kernel\Http\Router\Datastructures\Controller) {
            $this->setController($closure, $action->getClosureParameters());
        } elseif (is_array($closure)) {
            $uri = (new Kernel\Http\Message\Uri())
                ->withSegments(new Kernel\Http\Message\Uri\Segments(''))
                ->withQuery('');
            $this->parseRequest($uri->addSegments($closure));
        } else {
            if (class_exists($closure)) {
                $this->setController(
                    (new Kernel\Http\Router\Datastructures\Controller($closure))
                        ->setRequestMethod('index'),
                    $uriSegments
                );
            } elseif (preg_match("/([a-zA-Z0-9\\\]+)(@)([a-zA-Z0-9\\\]+)/", $closure, $matches)) {
                $this->setController(
                    (new Kernel\Http\Router\Datastructures\Controller($matches[ 1 ]))
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

    // ------------------------------------------------------------------------

    final protected function setPage(Router\Datastructures\Page $page)
    {
        foreach (modules()->getNamespaces() as $controllersNamespace) {
            $controllerPagesClassName = $controllersNamespace->name . 'Controllers\Pages';

            if ($controllersNamespace->name === 'O2System\Framework\\') {
                $controllerPagesClassName = 'O2System\Framework\Http\Controllers\Pages';
            }

            if (class_exists($controllerPagesClassName)) {
                $controller = new $controllerPagesClassName();

                if (method_exists($controller, 'setPage')) {
                    $controller->setPage($page);

                    $this->setController(
                        (new Kernel\Http\Router\Datastructures\Controller($controller))
                            ->setRequestMethod('index')
                    );

                    return true;
                }

                break;
            }
        }

        return false;
    }
}