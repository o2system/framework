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

use O2System\Framework\Http\Message\Request;
use O2System\Framework\Libraries\Acl\Datastructures\Account;
use O2System\Framework\Libraries\Acl\Sso\Broker;
use O2System\Spl\Datastructures\SplArrayObject;
use O2System\Spl\Exceptions\RuntimeException;
use O2System\Spl\Iterators\ArrayIterator;

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
            throw new RuntimeException('E_UNDEFINED_USERS_MODEL');
        }

        $this->attempts = (int)session()->offsetGet( 'aclAttempts' );
    }

    public function login( $username, $password, $remember = false )
    {
        $config = config( 'acl', true) ;

        if( empty( $config ) ) {
            $config = new SplArrayObject([
                'algorithm' => PASSWORD_DEFAULT,
                'options' => [ ],
                'msisdnRegex' => '/^\+[1-9]{1}[0-9]{3,14}$/'
            ]);
        }

        $condition = 'username';
        if( filter_var( $username, FILTER_VALIDATE_EMAIL ) ) {
            $condition = 'email';
        } elseif( preg_match( $config->msisdnRegex, $username ) ) {
            $condition = 'msisdn';
        }

        if ( ( $account = models( 'users' )->findAccount( $username, $condition ) ) instanceof Account
        ) {
            if ( password_verify( $password, $account->password ) ) {

                if( ! empty( $config->options ) ) {
                    $config->algorithm = PASSWORD_BCRYPT;
                }

                if ( password_needs_rehash(
                    $account->password,
                    $config->algorithm,
                    $config->options
                ) ) {
                    models( 'users' )->updateAccount(
                        new Account(
                            [
                                'username' => $account->username,
                                'password' => $password,
                            ]
                        )
                    );
                }

                // set user session
                unset( $account[ 'password' ], $account[ 'pin' ] );
                session()->offsetSet( 'account', $account );
                session()->offsetUnset( 'aclAttempts' );

                return true;
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
        if( session()->offsetExists('account') ) {
            return session()->offsetGet( 'account' );
        }

        return false;
    }

    public function getProfile( $scope = 'ALL' )
    {
        if( false !== ( $account = $this->getAccount() ) ) {
            return models( 'users' )->getProfile( $account->id, $scope );
        }

        return false;
    }

    public function getRoles()
    {
        if( false !== ( $account = $this->getAccount() ) ) {
            return models( 'users' )->getRoles( $account->id );
        }

        return false;
    }

    public function getRolesAccess()
    {
        if( false !== ( $account = $this->getAccount() ) ) {
            return models( 'users' )->getRolesAccess( $account->id );
        }

        return false;
    }

    public function authorize( Request $request )
    {
        static $roles;
        static $rolesAccess;

        if( empty( $roles ) ) {
            $roles = $this->getRoles();
        }

        foreach( $roles as $role ) {
            if( in_array( $role->code, [ 'DEVELOPER', 'ADMINISTRATOR' ], true ) ) {
                return true;
                break;
            }
        }

        if( empty( $rolesAccess ) ) {
            $rolesAccess = $this->getRolesAccess();
        }

        if( $rolesAccess instanceof ArrayIterator ) {
            $uriString = $request->getController()->getRequestSegments()->getString();
            if( false !== ( $access = $rolesAccess->offsetGet( $uriString ) ) ) {
                if( $access->permissions === 'GRANTED' ) {
                    return true;
                }
            }
        }

        return false;
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

        if ( isset( $redirectUrl ) ) {
            redirect_url( $redirectUrl );
        }
    }

    public function register( Account $account )
    {
        $model = models( 'users' );
        $model->db->transactionBegin();

        $model->insert( [
            'email'    => $account->email,
            'msisdn'   => $account->msisdn,
            'username' => $account->username,
            'password' => $account->password,
            'pin'      => $account->pin,
            'sso'      => $account->sso,
        ] );

        if ( $model->db->getTransactionStatus() ) {
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

            if ( $model->db->getTransactionStatus() ) {

                $model->role->insert( [
                    'id_sys_user'             => $id_sys_user,
                    'id_sys_module_user_role' => $account->role,
                ] );

                if ( $model->db->getTransactionStatus() ) {
                    $model->db->transactionCommit();
                }
            }
        }

        $model->db->transactionRollBack();

        return false;
    }
}