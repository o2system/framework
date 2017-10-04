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

use O2System\Psr\Patterns\AbstractItemStoragePattern;

/**
 * Class Role
 *
 * @package O2System\Framework\Libraries\Acl\Datastructures
 */
class Role extends AbstractItemStoragePattern
{
    /**
     * Role::__construct
     *
     * @param array $role
     */
    public function __construct( $role = [] )
    {
        $defaultRole = [
            'id'     => ( isset( $role[ 'id' ] ) ? $role[ 'id' ] : null ),
            'code'   => ( isset( $role[ 'code' ] ) ? $role[ 'code' ] : 'UNDEFINED' ),
            'label'  => ( isset( $role[ 'label' ] ) ? $role[ 'label' ] : 'Undefined' ),
            'description' => ( isset( $role[ 'description' ] ) ? $role[ 'description' ] : null ),
        ];

        foreach ($defaultRole as $item => $value ) {
            $this->store( $item, $value );
        }
    }

    public function __toString()
    {
        if( $this->offsetExists( 'label' ) ) {
            return $this->offsetGet( 'label' );
        }

        return '';
    }
}