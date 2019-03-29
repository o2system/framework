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

namespace O2System\Framework\Libraries\AccessControl;

// ------------------------------------------------------------------------

use O2System\Framework\Http\Message\ServerRequest;
use O2System\Security\Authentication\User\Account;
use O2System\Security\Authentication\User\Authorities;
use O2System\Security\Authentication\User\Authority;
use O2System\Security\Authentication\User\Role;
use O2System\Spl\Exceptions\RuntimeException;

/**
 * Class User
 * @package O2System\Framework\Libraries\AccessControl
 */
class User extends \O2System\Security\Authentication\User
{
    /**
     * User::__construct
     *
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    public function __construct()
    {
        parent::__construct();

        if ($config = config()->loadFile('AccessControl', true)) {
            $this->setConfig($config->getArrayCopy());
        }

        if ( ! models('users')) {
            throw new RuntimeException('ACL_E_UNDEFINED_USERS_MODEL');
        }
    }

    // ------------------------------------------------------------------------

    /**
     * User::authenticate
     *
     * @param string $username
     * @param string $password
     *
     * @return bool
     */
    public function authenticate($username, $password)
    {
        if ($user = $this->find($username)) {
            if ($user->account) {
                if ($this->passwordVerify($password, $user->account->password)) {
                    if ($this->passwordRehash($password)) {
                        $user->account->update([
                            'id'       => $user->id,
                            'password' => $this->passwordHash($password),
                        ]);
                    }

                    $account = $user->account->getArrayCopy();
                }
            } elseif ($this->passwordVerify($password, $user->password)) {
                $account = $user;
            }

            if (isset($account)) {
                foreach ($account as $key => $value) {
                    if (strpos($key, 'record') !== false) {
                        unset($account[ $key ]);
                    } elseif (in_array($key,
                        ['password', 'pin', 'token', 'sso', 'id_sys_user', 'id_sys_module', 'id_sys_module_role'])) {
                        unset($account[ $key ]);
                    }
                }

                $this->login($account);

                return true;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * User::find
     *
     * @param string $username
     *
     * @return bool|mixed|\O2System\Database\DataObjects\Result|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function find($username)
    {
        $column = 'username';
        if (is_numeric($username)) {
            $column = 'id';
        } elseif (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $column = 'email';
        } elseif (preg_match($this->config[ 'msisdnRegex' ], $username)) {
            $column = 'msisdn';
        }

        if ($user = models('users')->findWhere([$column => $username], 1)) {
            return $user;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * User::loggedIn
     *
     * @return bool
     * @throws \O2System\Psr\Cache\InvalidArgumentException
     */
    public function loggedIn()
    {
        if (parent::loggedIn()) {
            $account = new Account($_SESSION[ 'account' ]);

            if ($user = models('users')->findWhere(['username' => $account->username], 1)) {
                // Store Account Profile
                if ($profile = $user->profile) {
                    $account->store('profile', $profile);
                }

                // Store Account Role
                if ($role = $user->role) {
                    $account->store('role', new Role([
                        'label'       => $role->label,
                        'description' => $role->description,
                        'code'        => $role->code,
                        'authorities' => $role->authorities,
                    ]));
                }
            }
            
            // Store Globals Account
            globals()->store('account', $account);

            // Store Presenter Account
            if (services()->has('view')) {
                presenter()->store('account', $account);
            }

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * User::forceLogin
     *
     * @param string $username
     * @param string $column
     *
     * @return bool
     */
    public function forceLogin($username, $column = 'username')
    {
        if (is_numeric($username)) {
            $column = 'id';
        } elseif (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $column = 'email';
        } elseif (preg_match($this->config[ 'msisdnRegex' ], $username)) {
            $column = 'msisdn';
        } elseif (strpos($username, 'token-') !== false) {
            $username = str_replace('token-', '', $username);
            $column = 'token';
        } elseif (strpos($username, 'sso-') !== false) {
            $username = str_replace('sso-', '', $username);
            $column = 'sso';
        }

        if ($account = models('users')->findWhere([$column => $username], 1)) {
            $account = $account->getArrayCopy();

            foreach ($account as $key => $value) {
                if (strpos($key, 'record') !== false) {
                    unset($account[ $key ]);
                } elseif (in_array($key, ['password', 'pin', 'token', 'sso'])) {
                    unset($account[ $key ]);
                }
            }

            if ($column === 'token') {
                models('users')->update([
                    'id'    => $account[ 'id' ],
                    'token' => null,
                ]);
            }

            $this->login($account);

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * User::authorize
     *
     * @param \O2System\Framework\Http\Message\ServerRequest $request
     *
     * @return bool
     */
    public function authorize(ServerRequest $request)
    {
        if (isset($GLOBALS[ 'account' ][ 'role' ])) {
            $uriSegments = $request->getUri()->getSegments()->getString();
            $role = $GLOBALS[ 'account' ][ 'role' ];
            if (in_array($role->code, ['DEVELOPER', 'ADMINISTRATOR'])) {
                globals()->store('authority', new Authority([
                    'permission' => 'GRANTED',
                    'privileges' => '11111111',
                ]));

                return true;
            } elseif ($role->authorities instanceof Authorities) {
                if ($role->authorities->exists($uriSegments)) {
                    $authority = $role->authorities->getAuthority($uriSegments);

                    globals()->store('authority', $authority);

                    return $authority->getPermission();
                }

                globals()->store('authority', new Authority([
                    'permission' => 'DENIED',
                    'privileges' => '00000000',
                ]));

                return false;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * User::getIframeCode
     *
     * @return string
     * @throws \O2System\Psr\Cache\InvalidArgumentException
     */
    public function getIframeCode()
    {
        if ($this->signedOn() && $this->loggedIn() === false) {
            return '<iframe id="sign-on-iframe" width="1" height="1" src="' . rtrim($this->config[ 'sso' ][ 'server' ],
                    '/') . '" style="display: none; visibility: hidden;"></iframe>';
        }

        return '';
    }
}