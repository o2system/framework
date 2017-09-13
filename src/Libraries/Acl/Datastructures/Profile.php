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

use O2System\Framework\Datastructures\Commons\Metadata;
use O2System\Framework\Datastructures\Commons\Name;
use O2System\Psr\Patterns\AbstractDataStoragePattern;

/**
 * Class Profile
 *
 * @package O2System\Framework\Libraries\Acl\Datastructures
 */
class Profile extends AbstractDataStoragePattern
{
    /**
     * Profile::__construct
     *
     * @param array $profile
     */
    public function __construct( $profile = [] )
    {
        $defaultProfile = [
            'name'      => [
                'first'   => null,
                'middle'  => null,
                'last'    => null,
                'display' => null,
            ],
            'gender'    => 'UNDEFINED',
            'birthday'  => '0000-00-00',
            'marital'   => 'UNDEFINED',
            'religion'  => 'UNDEFINED',
            'biography' => null,
            'metadata'  => [],
        ];

        $profile = array_merge( $defaultProfile, $profile );

        foreach ( $profile as $item => $value ) {
            $this->store( $item, $value );
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Profile::store
     *
     * @param string $offset
     * @param mixed  $value
     */
    public function store( $offset, $value )
    {
        if ( $offset === 'name' ) {
            $value = new Name( $value );
        } elseif ( $offset === 'metadata' and is_array( $value ) ) {
            $value = new Metadata( $value );
        } elseif ( $offset === 'gender' ) {
            if ( in_array( $value, [ 'MALE', 'FEMALE', 'UNDEFINED' ] ) ) {
                $value = strtoupper( $value );
            } else {
                return;
            }
        } elseif ( $offset === 'marital' ) {
            if ( in_array( $value, [ 'SINGLE', 'MARRIED', 'DIVORCED', 'UNDEFINED' ] ) ) {
                $value = strtoupper( $value );
            } else {
                return;
            }
        } elseif ( $offset === 'religion' ) {
            if ( in_array( $value, [ 'HINDU', 'BUDDHA', 'MOSLEM', 'CHRISTIAN', 'CATHOLIC', 'UNDEFINED' ] ) ) {
                $value = strtoupper( $value );
            } else {
                return;
            }
        } elseif ( $offset === 'birthday' and $value !== '0000-00-00' ) {
            $value = date( 'Y-m-d', strtotime( $value ) );
        } elseif ( $offset === 'biography' ) {
            $value = trim( $value );
        }

        parent::store( $offset, $value );
    }
}