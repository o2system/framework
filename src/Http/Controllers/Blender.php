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

namespace O2System\Framework\Http\Controllers;

// ------------------------------------------------------------------------

use O2System\Framework\Http\Controller;

/**
 * Class Blender
 * @package O2System\Framework\Http\Controllers
 */
class Blender extends Controller
{
    public function index()
    {
        output()->sendPayload([
            'js' => 'cache/assets/app.js'
        ]);
    }
}