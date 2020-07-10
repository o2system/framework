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
                parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
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

        /**
         * ------------------------------------------------------------------------
         * Try to find modular using requested domain
         * ------------------------------------------------------------------------
         */
        if ( ( $addresses = config()->get('addresses') ) instanceof KernelAddresses) {
            if (null !== ($domain = $addresses->getDomain())) {
                if (is_array($domain)) {
                    modules()->find(reset($domain), true);
                }
            } elseif (false !== ($subdomain = $this->uri->getSubdomain())) {
                modules()->find($subdomain, true);
            }
        }

        /**
         * ------------------------------------------------------------------------
         * Try to find modular on uri segments
         * ------------------------------------------------------------------------
         */
        if ($numOfUriSegments = $this->uri->segments->count()) {
            modules()->find($this->uri->segments, true);
        }

        // Exchange the uri segments without modular segments
        $this->uri->segments->exchangeArray(array_diff($this->uri->segments->getArrayCopy(), modules()->top()->getSegments()));

        /**
         * ------------------------------------------------------------------------
         * Try to translate uri string
         * ------------------------------------------------------------------------
         */
        if (false !== ($action = config()->get('addresses')->getTranslation($this->uri->segments->__toString()))) {
            if (!$action->isValidHttpMethod(input()->server('REQUEST_METHOD')) && !$action->isAnyHttpMethod()) {
                output()->sendError(405);
            } else {
                if (is_array($closureSegments = $action->getClosure())) {
                    $this->uri->segments->exchangeArray($closureSegments);

                    modules()->find($this->uri->segments, true);

                    // Exchange the uri segments without modular segments
                    $this->uri->segments->exchangeArray(array_diff($this->uri->segments->getArrayCopy(), modules()->top()->getSegments()));
                } else {
                    if (false !== ($parseSegments = $action->getParseUriString($this->uri->segments->__toString()))) {
                        $uriSegments = $parseSegments;
                    } else {
                        $uriSegments = [];
                    }

                    if($action->getPath() !== '/') {
                        $this->uri = $this->uri->withSegments(new KernelMessageUriSegments($uriSegments));

                        $this->parseAction($action, $uriSegments);
                        if ( ! empty(services()->has('controller'))) {
                            return true;
                        }
                    }
                }
            }
        }

        /**
         * ------------------------------------------------------------------------
         * SPA Mode
         * ------------------------------------------------------------------------
         */
        if(config()->spa) {
            if($spaControllerClassName = $this->getControllerClassName('SinglePageApplication')) {
                $this->setController(
                    (new KernelControllerDataStructure($spaControllerClassName))
                        ->setRequestMethod('index')
                );

                return true;
            }
        }

        if ($numOfUriSegments = $this->uri->segments->count()) {
            /**
             * ------------------------------------------------------------------------
             * Try to find controller
             * ------------------------------------------------------------------------
             */
            $uriSegments = $this->uri->segments->getArrayCopy();

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
                            return true;
                            break; // break modular
                            break; // break routed uri segments
                        } else {
                            $uriSegments = array_diff($uriSegments, $uriRoutedSegments);
                            $this->setController(new KernelControllerDataStructure($controllerClassName),
                                $uriSegments);
                            return true;
                            break; // break modular
                            break; // break routed uri segments
                        }
                    }
                }
            }

            /**
             * ------------------------------------------------------------------------
             * Try to find static page
             * ------------------------------------------------------------------------
             */
            if (false !== ($pageFilePath = view()->getPageFilePath($this->uri->segments->__toString()))) {
                presenter()->page->setFile($pageFilePath);

                if($spaControllerClassName = $this->getControllerClassName('Pages')) {
                    if (presenter()->page->file instanceof SplFileInfo) {
                        $this->setController(
                            (new KernelControllerDataStructure($spaControllerClassName))
                                ->setRequestMethod('index')
                        );

                        return true;
                    }
                }
            } elseif (class_exists($pagesModelClassName = modules()->top()->getNamespace() . 'Models\Pages')) {
                models()->load($pagesModelClassName, 'controller');

                if (false !== ($page = models('controller')->findWhere([
                        'slug' => $this->uri->segments->__toString()
                    ], 1))) {
                    if (isset($page->content)) {
                        foreach($page as $offset => $value) {
                            presenter()->page->offsetSet($offset, $value);
                        }

                        if (class_exists($spaControllerClassName = modules()->top()->getNamespace() . 'Controllers\Pages')) {
                            $this->setController(
                                (new KernelControllerDataStructure($spaControllerClassName))
                                    ->setRequestMethod('index')
                            );
                        }

                        return true;
                    }
                }
            }
        }

        /**
         * ------------------------------------------------------------------------
         * Try to find module default controller
         * ------------------------------------------------------------------------
         */
        if (class_exists($controllerClassName = modules()->top()->getDefaultControllerClassName())) {
            $this->setController(new KernelControllerDataStructure($controllerClassName),
                $this->uri->segments->getArrayCopy());

            return true;
        } elseif (false !== ($action = config()->get('addresses')->getTranslation('/'))) {
            $this->parseAction($action, $this->uri->segments->getArrayCopy());
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
            $_SERVER['CONTENT_TYPE'] = 'application/json';

            $lastSegment = str_replace('.json', '', $lastSegment);
            $this->uri->segments->pop();
            $this->uri->segments->push($lastSegment);
        } elseif (strpos($lastSegment, '.xml') !== false) {
            output()->setContentType('application/xml');
            $_SERVER['CONTENT_TYPE'] = 'application/xml';

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
        } elseif (strpos($lastSegment, '.txt') !== false) {
            output()->setContentType('text/plain');
            $lastSegment = str_replace('.txt', '', $lastSegment);
            $this->uri->segments->pop();
            $this->uri->segments->push($lastSegment);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Router::getControllerClassName
     *
     * @return bool|string
     */
    final protected function getControllerClassName($className)
    {
        $modules = modules()->getArrayCopy();

        foreach ($modules as $module) {
            $controllerClassName = $module->getNamespace() . 'Controllers\\' . $className;
            if ($module->getNamespace() === 'O2System\Framework\\') {
                $controllerClassName = 'O2System\Framework\Http\Controllers\\' . $className;
            }

            if (class_exists($controllerClassName)) {
                return $controllerClassName;
                break;
            }
        }

        if (class_exists('O2System\Framework\Http\Controllers\\' . $className)) {
            return 'O2System\Framework\Http\Controllers\\' . $className;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Router::parseAction
     *
     * @param KernelActionDataStructure $action
     * @param array $uriSegments
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
                    (new KernelControllerDataStructure($matches[1]))
                        ->setRequestMethod($matches[3]),
                    $uriSegments
                );
            } elseif ($theme = presenter()->theme) {
                if ($theme->use === true) {
                    if (!presenter()->partials->offsetExists('content') && $closure !== '') {
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
