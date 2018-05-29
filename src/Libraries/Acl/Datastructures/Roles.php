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

namespace O2System\Framework\Libraries\Acl\Datastructures;

// ------------------------------------------------------------------------

use O2System\Psr\Patterns\Structural\Provider\AbstractProvider;

/**
 * Class Roles
 *
 * @package O2System\Framework\Libraries\Acl\Datastructures
 */
class Roles extends AbstractProvider
{
    public function primary()
    {
        if ($this->exists('DEVELOPER')) {
            return $this->getObject('DEVELOPER');
        } elseif ($this->exists('ADMINISTRATOR')) {
            return $this->getObject('ADMINISTRATOR');
        }

        $iterator = $this->getIterator();

        return $iterator->first();
    }

    /**
     * Roles::isValid
     *
     * Checks if the object is a valid instance.
     *
     * @param object $object The object to be validated.
     *
     * @return bool Returns TRUE on valid or FALSE on failure.
     */
    protected function isValid($object)
    {
        if ($object instanceof Roles\Role) {
            return true;
        }

        return false;
    }
}