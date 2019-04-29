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

namespace O2System\Framework\Cli;

// ------------------------------------------------------------------------

/**
 * Class Commander
 * @package O2System\Framework\Cli
 */
abstract class Commander extends \O2System\Kernel\Cli\Commander
{
    /**
     * Commander::__get
     *
     * @param string $property
     *
     * @return mixed|\O2System\Framework\Containers\Models|\O2System\Framework\Models\NoSql\Model|\O2System\Framework\Models\Sql\Model
     */
    public function &__get($property)
    {
        $get[ $property ] = false;

        // CodeIgniter property aliasing
        if ($property === 'load') {
            $property = 'loader';
        }

        if (services()->has($property)) {
            return services()->get($property);
        } elseif (o2system()->__isset($property)) {
            return o2system()->__get($property);
        } elseif ($property === 'model') {
            return models('controller');
        }

        return $get[ $property ];
    }
}