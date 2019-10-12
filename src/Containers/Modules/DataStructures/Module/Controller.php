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

namespace O2System\Framework\Containers\Modules\DataStructures\Module;

// ------------------------------------------------------------------------

use O2System\Spl\Info\SplClassInfo;

/**
 * Class Controller
 * @package O2System\Framework\Containers\Modules\DataStructures\Module
 */
class Controller extends SplClassInfo
{
    /**
     * Controller::$methods
     *
     * @var array
     */
    public $methods = [];

    /**
     * Controller::$namespace
     *
     * @var string
     */
    public $namespace;

    // ------------------------------------------------------------------------

    /**
     * Controller::__construct
     *
     * @param string $className
     */
    public function __construct($className)
    {
        parent::__construct($className);

        if ( ! empty($this->name)) {
            $this->namespace = get_namespace($className);

            $nameParts = explode('\\', $this->name);

            $segments = [];
            foreach ($nameParts as $namePart) {
                if ( ! in_array(strtolower($namePart), [
                    'app',
                    'apps',
                    'modules',
                    'components',
                    'plugins',
                    'controllers',
                ])) {
                    $namePart = dash($namePart);

                    if ( ! in_array($namePart, $segments)) {
                        array_push($segments, $namePart);
                    }
                }
            }

            if ($methods = $this->getMethods(\ReflectionMethod::IS_PUBLIC)) {
                foreach ($methods as $method) {
                    if (strpos($method->name, '__') === false and ! in_array($method->name,
                            [
                                'route',
                                'getClassInfo',
                                'form',
                                'add',
                                'add-new',
                                'edit',
                                'create',
                                'read',
                                'update',
                                'delete',
                                'open',
                                'download',
                                'detail',
                                'overview',
                                'view',
                                'settings',
                                'setting',
                                'sendError',
                                'sendPayload'
                            ])) {
                        $methodSegments = $segments;

                        $method->segment = dash($method->name);

                        if ( ! in_array($method->name, $methodSegments) and $method->name !== 'index') {
                            array_push($methodSegments, dash($method->name));
                        }

                        $method->segments = implode('/', $methodSegments);
                        $method->hash = md5($method->segments);

                        $this->methods[$method->segment] = $method;
                    }
                }
            }

            if(empty($this->methods) and $this->hasMethod('route')) {
                $method = $this->getMethod('route');
                $method->segment = dash($method->name);
                $method->segments = implode('/', $segments);
                $method->hash = md5($method->segments);
                $this->methods[$method->segment] = $method;
            }
        }
    }
}