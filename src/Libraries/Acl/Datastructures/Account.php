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

use O2System\Psr\Patterns\AbstractDataStoragePattern;

/**
 * Class Account
 *
 * @package O2System\Framework\Libraries\Acl\Datastructures
 */
class Account extends AbstractDataStoragePattern
{
    /**
     * Account::__construct
     *
     * @param array $account
     * @param bool  $hash Hashed password and pin options.
     */
    public function __construct( $account = [], $hash = true )
    {
        $defaultAccount = [
            'email'    => null,
            'msisdn'   => null,
            'username' => null,
            'password' => null,
            'pin'      => null,
            'role'     => null,
            'profile'  => null,
        ];

        foreach ( $defaultAccount as $item => $value ) {
            if ( in_array( $item, [ 'password', 'pin' ] ) and $hash === true ) {
                /**
                 * This code will benchmark your server to determine how high of a cost you can
                 * afford. You want to set the highest cost that you can without slowing down
                 * you server too much. 8-10 is a good baseline, and more is good if your servers
                 * are fast enough. The code below aims for ≤ 50 milliseconds stretching time,
                 * which is a good baseline for systems handling interactive logins.
                 */
                $cost = 8;
                do {
                    $cost++;
                    $start = microtime( true );
                    password_hash( $account[ $item ], PASSWORD_BCRYPT, [ 'cost' => $cost ] );
                    $end = microtime( true );
                } while ( ( $end - $start ) < 0.05 ); // 50 milliseconds

                $this->store(
                    $item,
                    password_hash( $account[ $item ], PASSWORD_BCRYPT, [ 'cost' => $cost ] )
                );
            } elseif ( isset( $account[ $item ] ) ) {

                if ( $item === 'profile' ) {
                    $account[ $item ] = new Profile( $account[ $item ] );
                }

                $this->store( $item, $account[ $item ] );
            } else {
                $this->store( $item, null );
            }
        }
    }
}