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

namespace O2System\Framework\Libraries\Acl;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Acl\Datastructures\Account;
use O2System\Framework\Libraries\Acl\Datastructures\Profile;
use O2System\Framework\Libraries\Acl\Datastructures\SignedOn;
use O2System\Framework\Libraries\Acl\Sso\Broker;
use O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException;

/**
 * Class User
 *
 * @package O2System\Framework\Libraries\Acl
 */
class User
{
    protected $broker;
    protected $attempts = 0;

    public function __construct()
    {
        if ( ! models( 'users' ) ) {
            models()->load( 'O2System\Framework\Models\SQL\System\Users' );
        }

        $this->attempts = (int)session()->offsetGet( 'aclAttempts' );
    }

    public function login( $username, $password, $remember = false )
    {
        if ( ( $account = models( 'users' )->findWhere(
                [ 'username' => $username, 'record_status' => 'PUBLISH' ],
                1
            ) ) instanceof Account
        ) {
            $info = password_get_info( $account->password );
            if ( password_verify( $password, $account->password ) ) {
                if ( password_needs_rehash(
                    $account->password,
                    $info[ 'algo' ],
                    [ 'cost' => $info[ 'options' ][ 'cost' ] ]
                ) ) {
                    models( 'users' )->update(
                        new Account(
                            [
                                'username' => $account->username,
                                'password' => $password,
                            ]
                        )
                    );
                }

                if ( ( $profile = models( 'users' )->profile->find( $account->id,
                        'id_sys_user' ) ) instanceof Profile
                ) {
                    // set session
                    unset( $account[ 'password' ], $account[ 'pin' ] );
                    session()->offsetSet( 'account', $account );
                    session()->offsetSet( 'profile', $profile );
                    session()->offsetUnset( 'aclAttempts' );

                    return true;
                }
            }
        }

        session()->offsetSet( 'aclAttempts', ++$this->attempts );

        return false;
    }

    public function getAttempts()
    {
        return (int)$this->attempts;
    }

    public function getAccount()
    {
        return session()->offsetGet( 'account' );
    }

    public function getProfile()
    {
        return session()->offsetGet( 'profile' );
    }

    public function loggedIn()
    {
        if ( $this->broker instanceof Broker ) {
            return (bool)$this->broker->signIn();
        }

        return (bool)session()->offsetExists( 'account' );
    }

    public function logout( $redirectUrl = null )
    {
        session()->offsetUnset( 'account' );
        session()->offsetUnset( 'profile' );

        if ( isset( $redirectUrl ) ) {
            redirect_url( $redirectUrl );
        }
    }

    public function register( Account $account )
    {
        $model = models( 'users' );
        $model->connection->transactionBegin();

        $model->insert( [
            'email'    => $account->email,
            'msisdn'   => $account->msisdn,
            'username' => $account->username,
            'password' => $account->password,
            'pin'      => $account->pin,
            'sso'      => $account->sso,
        ] );

        if ( $model->connection->getTransactionStatus() ) {
            $id_sys_user = $model->connection->getLastInsertId();

            $model->profile->insert( [
                'id_sys_user'  => $id_sys_user,
                'name_first'   => $account->profile->name->first,
                'name_middle'  => $account->profile->name->middle,
                'name_last'    => $account->profile->name->last,
                'name_display' => $account->profile->name->display,
                'gender'       => $account->profile->gender,
                'birthday'     => $account->profile->birthday,
                'marital'      => $account->profile->marital,
                'religion'     => $account->profile->religion,
                'biography'    => $account->profile->biography,
                'metadata'     => $account->profile->metadata,
            ] );

            if ( $model->connection->getTransactionStatus() ) {

                $model->role->insert( [
                    'id_sys_user'             => $id_sys_user,
                    'id_sys_module_user_role' => $account->role,
                ] );

                if ( $model->connection->getTransactionStatus() ) {
                    $model->connection->transactionCommit();
                }
            }
        }

        $model->connection->transactionRollBack();

        return false;
    }
}