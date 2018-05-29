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

if ( ! function_exists('o2system')) {
    /**
     * o2system
     *
     * Convenient shortcut for O2System Framework Instance
     *
     * @return O2System\Framework
     */
    function o2system()
    {
        return O2System\Framework::getInstance();
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('loader')) {
    /**
     * loader
     *
     * Convenient shortcut for O2System Framework Loader service.
     *
     * @return O2System\Framework\Services\Loader
     */
    function loader()
    {
        return o2system()->getService('loader');
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('config')) {
    /**
     * config
     *
     * Convenient shortcut for O2System Framework Config service.
     *
     * @return O2System\Framework\Services\Config|\O2System\Kernel\Datastructures\Config
     */
    function config()
    {
        $args = func_get_args();

        if ($countArgs = count($args)) {
            $config =& o2system()->getService('config');

            if ($countArgs == 1) {
                return call_user_func_array([&$config, 'getItem'], $args);
            } else {
                return call_user_func_array([&$config, 'loadFile'], $args);
            }
        }

        return o2system()->getService('config');
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('cache')) {
    /**
     * cache
     *
     * Convenient shortcut for O2System Framework Cache service.
     *
     * @return O2System\Framework\Services\Cache
     */
    function cache()
    {
        return o2system()->getService('cache');
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('hooks')) {
    /**
     * hooks
     *
     * Convenient shortcut for O2System Framework Hooks service.
     *
     * @return O2System\Framework\Services\Hooks
     */
    function hooks()
    {
        return o2system()->getService('hooks');
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('database')) {
    /**
     * database
     *
     * Convenient shortcut for O2System Framework Database Connection pools.
     *
     * @return O2System\Database\Connections
     */
    function database()
    {
        return o2system()->__get('database');
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('services')) {
    /**
     * services
     *
     * Convenient shortcut for O2System Framework Services container.
     *
     * @return mixed
     */
    function services()
    {
        $args = func_get_args();

        if (count($args)) {
            return o2system()->getService($args[ 0 ], true);
        }

        return o2system();
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('models')) {
    /**
     * models
     *
     * Convenient shortcut for O2System Framework Models container.
     *
     * @return O2System\Framework\Containers\Models|O2System\Framework\Models\Sql\Model|O2System\Framework\Models\NoSql\Model
     */
    function &models()
    {
        $args = func_get_args();

        if (count($args)) {
            return o2system()->__get('models')->get($args[ 0 ]);
        }

        return o2system()->__get('models');
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('modules')) {
    /**
     * modules
     *
     * Convenient shortcut for O2System Framework Modules container.
     *
     * @return O2System\Framework\Containers\Modules
     */
    function modules()
    {
        return o2system()->__get('modules');
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('router')) {
    /**
     * router
     *
     * Convenient shortcut for O2System Framework Router service.
     *
     * @return O2System\Framework\Http\Router|O2System\Framework\Cli\Router
     */
    function router()
    {
        return o2system()->getService('router');
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('session')) {
    /**
     * session
     *
     * Convenient shortcut for O2System Framework Session service.
     *
     * @return O2System\Session
     */
    function session()
    {
        return o2system()->getService('session');
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('middleware')) {
    /**
     * O2System
     *
     * Convenient shortcut for O2System Framework Http Middleware service.
     *
     * @return O2System\Framework\Http\Middleware
     */
    function middleware()
    {
        return o2system()->getService('middleware');
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('view')) {
    /**
     * view
     *
     * Convenient shortcut for O2System Framework View service.
     *
     * @return O2System\Framework\Http\View|string
     */
    function view()
    {
        $args = func_get_args();

        if (count($args)) {
            $view =& o2system()->getService('view');

            return call_user_func_array([&$view, 'load'], $args);
        }

        return o2system()->getService('view');
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('parser')) {
    /**
     * parser
     *
     * Convenient shortcut for O2System Parser service.
     *
     * @return O2System\Framework\Http\Parser
     */
    function parser()
    {
        return o2system()->getService('parser');
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('presenter')) {
    /**
     * presenter
     *
     * Convenient shortcut for O2System Framework Http Presenter service.
     *
     * @return O2System\Framework\Http\Presenter|object
     */
    function presenter()
    {
        return o2system()->getService('presenter');
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('controller')) {
    /**
     * controller
     *
     * Convenient shortcut for O2System Framework Controller service.
     *
     * @return O2System\Framework\Http\Controller|bool
     */
    function controller()
    {
        $args = func_get_args();

        if (count($args)) {
            $controller =& o2system()->getService('controller');

            return call_user_func_array([&$controller, '__call'], $args);
        }

        return o2system()->getService('controller');
    }
}

// ------------------------------------------------------------------------