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

namespace O2System\Framework\Services;

// ------------------------------------------------------------------------

use O2System\Security\Authentication\User\Account;
use O2System\Security\Authentication\User\Role;
use O2System\Spl\Exceptions\RuntimeException;

/**
 * Class AccessControl
 * @package O2System\Framework\Services
 */
class AccessControl extends \O2System\Security\Authentication\User
{
    /**
     * User::$app
     *
     * @var string
     */
    protected $app = 'app';

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
     * User::setApp
     *
     * @param string $app
     *
     * @return static
     */
    public function setApp($app)
    {
        if ($app = modules()->getApp($app)) {
            $this->app = $app;
        }

        return $this;
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
                $account = $user->getArrayCopy();
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

        if ($user = models('users')->findWhere([
            $column => $username,
        ], 1)) {
            return $user;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * User::loggedIn
     *
     * @return bool
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function loggedIn()
    {
        if (parent::loggedIn()) {
            if(is_object($_SESSION['account'])) {
                $account = new Account($_SESSION['account']->getArrayCopy());
                $username = $account->user->username;
            } else {
                $account = new Account();
                $username = $_SESSION['account']['username'];
            }

            if ($user = models('users')->findWhere(['username' => $username], 1)) {
                if ($profile = $user->profile) {
                    $account->store('profile', $profile);
                }

                if ($employee = $user->employee) {
                    $account->store('employee', $employee);
                }

                if ($member = $user->member) {
                    $account->store('member', $member);
                }

                if ($role = $user->role) {
                    $user->store('role', $role);
                }

                $account->store('user', $user);

                session()->set('account', $account);
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
     * User::hasAccess
     *
     * @param array $segments
     *
     * @return bool
     */
    public function hasAccess(array $segments)
    {
        if ($this->loggedIn()) {
            if ($account = globals()->offsetGet('account')) {
                if (in_array($account->user->role->id, [1, 2])) {
                    return true;
                }

                if ($model = models('modules')) {
                    if ($segment = $model->segments->find(implode('/', $segments), 'segments')) {
                        if ($authority = $model->segments->authorities->users->findWhere([
                            'id_sys_module_segment' => $segment->id,
                            'id_sys_module_user'    => $account->user->moduleUser->id,
                        ])) {
                            if ($authority->first()->permission === 'WRITE') {
                                return true;
                            } elseif ($authority->first()->permission === 'GRANTED') {
                                // Access only granted cannot do modifier access
                                foreach ([
                                             'form',
                                             'add',
                                             'add-as-new',
                                             'edit',
                                             'update',
                                             'insert',
                                             'create',
                                             'delete',
                                         ] as $segment
                                ) {
                                    if (in_array($segment, $segments)) {
                                        return false;
                                    }
                                }

                                return true;
                            }
                        }

                        if ($authority = $model->segments->authorities->roles->findWhere([
                            'id_sys_module_segment' => $segment->id,
                            'id_sys_module_role'    => $account->user->role->id,
                        ])) {
                            if ($authority->first()->permission === 'WRITE') {
                                return true;
                            } elseif ($authority->first()->permission === 'GRANTED') {
                                // Access only granted cannot do modifier access
                                foreach ([
                                             'form',
                                             'add',
                                             'add-as-new',
                                             'edit',
                                             'update',
                                             'insert',
                                             'create',
                                             'delete',
                                         ] as $segment
                                ) {
                                    if (in_array($segment, $segments)) {
                                        return false;
                                    }
                                }

                                return true;
                            }
                        }
                    }
                }
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * User::hasWriteAccess
     *
     * @return bool
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function hasWriteAccess()
    {
        $segments = server_request()->getUri()->segments->getArrayCopy();

        if ($this->loggedIn()) {
            if ($account = globals()->offsetGet('account')) {
                if (in_array($account->user->role->id, [1, 2])) {
                    return true;
                }

                if ($model = models('modules')) {

                    if ($segment = $model->segments->find(implode('/', $segments), 'segments')) {
                        if ($authority = $model->segments->authorities->users->findWhere([
                            'id_sys_module_segment' => $segment->id,
                            'id_sys_module_user'    => $account->user->moduleUser->id,
                        ])) {
                            if ($authority->first()->permission === 'WRITE') {
                                return true;
                            }
                        }

                        if ($authority = $model->segments->authorities->roles->findWhere([
                            'id_sys_module_segment' => $segment->id,
                            'id_sys_module_role'    => $account->user->role->id,
                        ])) {
                            if ($authority->first()->permission === 'WRITE') {
                                return true;
                            }
                        }
                    }
                }
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * User::getIframeCode
     *
     * @return string
     * @throws \Psr\Cache\InvalidArgumentException
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