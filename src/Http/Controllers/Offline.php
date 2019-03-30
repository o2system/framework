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
 * Class Offline
 * @package O2System\Framework\Http\Controllers
 */
class Offline extends Controller
{
    public function index()
    {
        view('offline', [
            'title' => language('OFFLINE_TITLE'),
            'message' => language('OFFLINE_MESSAGE')
        ]);
    }
}