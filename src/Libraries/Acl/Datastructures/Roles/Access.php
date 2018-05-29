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

use O2System\Psr\Patterns\Structural\Repository\AbstractRepository;

/**
 * Class Access
 *
 * @package O2System\Framework\Libraries\Acl\Datastructures\Role
 */
class Access extends AbstractRepository
{
    /**
     * Access::__construct
     *
     * @param array $access
     */
    public function __construct($access = [])
    {
        $defaultAccess = [
            'id'         => isset($access[ 'id' ]) ? $access[ 'id' ] : null,
            'label'      => isset($access[ 'label' ]) ? $access[ 'label' ] : null,
            'segments'   => isset($access[ 'segments' ]) ? $access[ 'segments' ] : null,
            'permission' => isset($access[ 'permission' ]) ? $access[ 'permission' ] : 'DENIED',
            'privileges' => isset($access[ 'privileges' ]) ? $access[ 'privileges' ] : '00000000',
        ];

        foreach ($defaultAccess as $item => $value) {
            $this->store($item, $value);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Access::store
     *
     * @param string $offset
     * @param mixed  $value
     */
    public function store($offset, $value)
    {
        if ($offset === 'permission') {
            if (in_array($value, ['GRANTED', 'DENIED'])) {
                $value = strtoupper($value);
            } else {
                return;
            }
        } elseif ($offset === 'privileges') {
            list($create, $read, $update, $delete, $import, $export, $print, $special) = array_pad(str_split($value), 8,
                0);
            $value = [
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

        parent::store($offset, $value);
    }
}