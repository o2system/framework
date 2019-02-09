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

use O2System\Security\Filters\Xss;

/**
 * Class Input
 * @package O2System\Framework\Http
 */
class Input extends \O2System\Kernel\Http\Input
{
    /**
     * Input::filter
     *
     * @param int  $type
     * @param null $offset
     * @param int  $filter
     *
     * @return mixed|\O2System\Spl\DataStructures\SplArrayObject|string
     */
    protected function filter($type, $offset = null, $filter = FILTER_DEFAULT)
    {
        if (services()->has('xssProtection')) {
            if ( ! services()->get('xssProtection')->verify()) {
                $string = parent::filter($type, $offset, $filter);

                if (is_string($string)) {
                    return Xss::clean($string);
                }
            }
        }

        return parent::filter($type, $offset, $filter);
    }
}