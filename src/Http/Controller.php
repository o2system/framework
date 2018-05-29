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

namespace O2System\Framework\Http;

// ------------------------------------------------------------------------

/**
 * Class Controller
 *
 * @package O2System\Framework\Http
 */
class Controller extends \O2System\Kernel\Http\Controller
{
    public function &__get($property)
    {
        $get[ $property ] = false;

        // CodeIgniter property aliasing
        if ($property === 'load') {
            $property = 'loader';
        }

        if (o2system()->hasService($property)) {
            return o2system()->getService($property);
        } elseif (o2system()->__isset($property)) {
            return o2system()->__get($property);
        } elseif ($property === 'model') {
            return models('controller');
        }

        return $get[ $property ];
    }
}