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
use O2System\Spl\Info\SplFileInfo;

/**
 * Class Pages
 *
 * @package O2System\Framework\Http\Controllers
 */
class Pages extends Controller
{
    /**
     * Pages::index
     *
     * @return void
     */
    public function index()
    {
        if(presenter()->page->file instanceof SplFileInfo) {
            view()->page(presenter()->page->file->getRealPath());
        }
    }
}