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
 * Class Language
 * @package O2System\Framework\Http\Controllers
 */
class Language extends Controller
{
    /**
     * Language::index
     */
    public function index()
    {
        if($changeTo = input()->get('change')) {
            language()->change($changeTo);
        }

        if($referrerUrl = input()->server('HTTP_REFERRER')) {
            redirect_url($referrerUrl);
        } else {
            redirect_url(base_url());
        }
    }
}