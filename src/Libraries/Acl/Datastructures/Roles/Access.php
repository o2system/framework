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

namespace O2System\Framework\Libraries\Acl\Datastructures\Roles;

// ------------------------------------------------------------------------

use O2System\Spl\Iterators\ArrayIterator;

/**
 * Class Access
 *
 * @package O2System\Framework\Libraries\Acl\Datastructures\Role
 */
class Access extends ArrayIterator
{
    /**
     * Access::offsetSet
     *
     * @param string $offset
     * @param mixed  $value
     */
    public function offsetSet($offset, $value)
    {
        if (isset($value['permissions'])) {
            if (in_array($value, ['GRANTED', 'DENIED'])) {
                $value = strtoupper($value['permissions']);
            } else {
                return;
            }
        } elseif (isset($value['privileges'])) {
            list($create, $read, $update, $delete, $import, $export, $print, $special) = array_pad(str_split($value['privileges']), 8,
                0);
            $value['privileges'] = [
                'create'  => ($create == '1' ? true : false),
                'read'    => ($read == '1' ? true : false),
                'update'  => ($update == '1' ? true : false),
                'delete'  => ($delete == '1' ? true : false),
                'import'  => ($import == '1' ? true : false),
                'export'  => ($export == '1' ? true : false),
                'print'   => ($print == '1' ? true : false),
                'special' => ($special == '1' ? true : false),
            ];
        }

        parent::offsetSet($offset, $value);
    }
}