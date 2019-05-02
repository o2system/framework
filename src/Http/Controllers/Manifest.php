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
 * Class Manifest
 * @package O2System\Framework\Http\Controllers
 */
class Manifest extends Controller
{
    /**
     * Manifest::$inherited
     *
     * Controller inherited flag.
     *
     * @var bool
     */
    static public $inherited = true;

    // ------------------------------------------------------------------------

    /**
     * Manifest::index
     */
    public function index()
    {
        if (false !== ($manifest = $this->config->loadFile('manifest', true))) {
            output()->sendPayload([
                'short_name'           => $manifest->shortName,
                'name'                 => $manifest->name,
                'description'          => $manifest->description,
                'icons'                => array_values($manifest->icons),
                'start_url'            => empty($manifest->startUrl) ? '/' : $manifest->startUrl,
                'display'              => $manifest->display,
                'orientation'          => $manifest->orientation,
                'theme_color'          => $manifest->themeColor,
                'background_color'     => $manifest->backgroundColor,
                'related_applications' => empty($manifest->relatedApplications) ? [] : $manifest->relatedApplications,
            ]);
        }
    }
}