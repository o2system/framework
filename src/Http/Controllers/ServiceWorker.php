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

namespace O2System\Framework\Http\Controllers;

// ------------------------------------------------------------------------

use O2System\Framework\Http\Controller;

/**
 * Class ServiceWorker
 * @package O2System\Framework\Http\Controllers
 */
class ServiceWorker extends Controller
{
    /**
     * ServiceWorker::$inherited
     *
     * Controller inherited flag.
     *
     * @var bool
     */
    static public $inherited = true;

    // ------------------------------------------------------------------------

    /**
     * ServiceWorker::index
     */
    public function index()
    {
        if ($javascript = file_get_contents(PATH_RESOURCES . 'service-worker.js')) {
            $appName = globals()->offsetGet('app')->getParameter();
            
            $javascript = str_replace([
                '{{$appName}}',
                '{{ $appName }}',
            ], globals()->offsetGet('app')->getParameter(), $javascript);

            if (! presenter()->theme) {
                $javascript = str_replace([
                    '{{$themeName}}',
                    '{{ $themeName }}',
                ], presenter()->theme->getParameter(), $javascript);
            } else {
                $javascript = str_replace([
                    '{{$themeName}}',
                    '{{ $themeName }}',
                ], null, $javascript);
            }

            header("Content-type: application/x-javascript");
            echo $javascript;

            exit(EXIT_SUCCESS);
        }
    }
}