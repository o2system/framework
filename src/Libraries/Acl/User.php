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

use O2System\Framework\Http\Message\ServerRequest;
use O2System\Framework\Libraries\Acl\Datastructures\Account;
use O2System\Framework\Libraries\Acl\Datastructures\Signature;
use O2System\Spl\Exceptions\RuntimeException;
use O2System\Spl\Iterators\ArrayIterator;

/**
 * Class User
 *
 * @package O2System\Framework\Libraries\Acl
 */
class User
{
    protected $algorithm = PASSWORD_DEFAULT;
    protected $options = [];
    protected $msisdnRegex = '/^\+[1-9]{1}[0-9]{3,14}$/';
    protected $maxAttempts = 5;
    protected $currentAttempts = 0;
    protected $sso = [
        'enable' => false,
        'server' => null,
    ];


    // ------------------------------------------------------------------------

    public function __construct()
    {
        language()->loadFile('acl');

        if ( ! models('users')) {
            throw new RuntimeException('ACL_E_UNDEFINED_USERS_MODEL');
        }

        if ($config = config()->loadFile('acl', true)) {

            if ($config->offsetExists('algorithm')) {
                $this->algorithm = $config->algorithm;
            }

            if ($config->offsetExists('options')) {
                $this->options = (array)$config->options;
            }

            if ($config->offsetExists('msisdnRegex')) {
                $this->msisdnRegex = (string)$config->msisdnRegex;
            }

            if ($config->offsetExists('attempts')) {
                $this->maxAttempts = (int)$config->attempts;
            }

            if ($config->offsetExists('sso')) {
                $this->sso = $config->sso->getArrayCopy();
            }
        }

        $this->currentAttempts = (int)session()->offsetGet('aclAttempts');
    }


    // ------------------------------------------------------------------------

    public function login($username, $password = null, $remember = false)
    {
        if ($username instanceof Account) {
            $this->setSession($username);
        } elseif (false !== ($account = $this->findAccount($username))) {
            if (password_verify($password, $account->password)) {

                if (password_needs_rehash(
                    $account->password,
                    $this->algorithm,
                    $this->options
                )) {
                    models('users')->updateAccount(
                        new Account(
                            [
                                'id'       => $account->id,
                                'email'    => $account->email,
                                'msisdn'   => $account->msisdn,
                                'username' => $account->username,
                                'password' => $account->password,
                                'pin'      => $account->pin,
                            ]
                        )
                    );
                }

                $this->setSession($account);

                return true;
            }
        }

        session()->offsetSet('aclAttempts', ++$this->currentAttempts);

        return false;
    }

    // ------------------------------------------------------------------------

    protected function setSession(Account $account)
    {
        // set user single-sign-on (sso)
        if ($this->sso[ 'enable' ] === true) {
            if (method_exists(models('users'), 'insertSignature')) {
                models('users')->insertSignature(new Signature([
                    'id_sys_user' => $account->id,
                    'code'        => $account[ 'ssid' ] = md5(json_encode($account) . mt_srand() . time()),
                ]));

                set_cookie('ssid', $account[ 'ssid' ]);
            }
        }

        if ( ! empty($account->password)) {
            unset($account->password);
        }

        if ( ! empty($account->pin)) {
            unset($account->pin);
        }

        // set user session
        session()->offsetSet('account', $account);
        session()->offsetUnset('aclAttempts');
    }

    /**
     * User::findAccount
     *
     * @param string $username
     *
     * @return Account|bool Returns FALSE if failed.
     */
    public function findAccount($username)
    {
        $condition = 'username';
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $condition = 'email';
        } elseif (preg_match($this->msisdnRegex, $username)) {
            $condition = 'msisdn';
        }

        if (($account = models('users')->findAccount($username, $condition)) instanceof Account
        ) {
            return $account;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    public function getCurrentAttempts()
    {
        return (int)$this->currentAttempts;
    }

    // ------------------------------------------------------------------------

    public function getProfile($scope = 'ALL')
    {
        if (false !== ($account = $this->getAccount())) {
            if (method_exists(models('users'), 'getProfile')) {
                return models('users')->getProfile($account->id, $scope);
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    public function getAccount()
    {
        if ($this->loggedIn()) {
            return session()->offsetGet('account');
        }

        return false;
    }

    // ------------------------------------------------------------------------

    public function authorize(ServerRequest $request)
    {
        if (false !== ($roles = $this->getRoles())) {
            foreach ($roles as $role) {
                if (in_array($role->code, ['DEVELOPER', 'ADMINISTRATOR'], true)) {
                    return true;
                    break;
                }
            }
        } elseif (false !== ($rolesAccess = $this->getRolesAccess())) {
            if ($rolesAccess instanceof ArrayIterator) {
                $uriString = $request->getController()->getRequestSegments()->getString();
                if (false !== ($access = $rolesAccess->offsetGet($uriString))) {
                    if ($access->permissions === 'GRANTED') {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    public function getRoles()
    {
        if (false !== ($account = $this->getAccount())) {
            if (method_exists(models('users'), 'getRoles')) {
                return models('users')->getRoles($account->id);
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    public function getRolesAccess()
    {
        if (false !== ($account = $this->getAccount())) {
            if (method_exists(models('users'), 'getRolesAccess')) {
                return models('users')->getRolesAccess($account->id);
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    public function getIframeCode()
    {
        if ($this->sso[ 'enable' ] && $this->loggedIn() === false) {
            return '<iframe id="sign-on-iframe" width="1" height="1" src="' . rtrim($this->sso[ 'server' ],
                    '/') . '" style="display: none; visibility: hidden;"></iframe>';
        }

        return '';
    }

    // ------------------------------------------------------------------------

    public function loggedIn()
    {
        if ($this->sso[ 'enable' ] === true && session()->offsetExists('account') === false) {
            if ($token = get_cookie('ssid')) {
                return $this->validate($token);
            }
        }

        return (bool)session()->offsetExists('account');
    }

    // ------------------------------------------------------------------------

    public function validate($ssid)
    {
        if (method_exists(models('users'), 'findSignature')) {
            if (($account = models('users')->findSignature($ssid)) instanceof Account
            ) {
                // set user session
                unset($account[ 'password' ], $account[ 'pin' ]);
                session()->offsetSet('account', $account);

                if (method_exists(models('users'), 'deleteSignature')) {
                    models('users')->deleteSignature($account);
                }

                return true;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    public function getIframeScript()
    {
        return '<script>window.parent.location.reload();</script>';
    }

    // ------------------------------------------------------------------------

    public function logout()
    {
        if ($this->loggedIn()) {
            if (method_exists(models('users'), 'deleteSignature')) {
                models('users')->deleteSignature($this->getAccount());
            }
        }

        delete_cookie('ssid');

        session()->destroy();
    }

    // ------------------------------------------------------------------------

    /**
     * @todo: moved into model user
     *
     * @param \O2System\Framework\Libraries\Acl\Datastructures\Account $account
     *
     * @return bool
     */
    public function register(Account $account)
    {
        $model = models('users');
        $model->db->transactionBegin();

        $model->insert([
            'email'    => $account->email,
            'msisdn'   => $account->msisdn,
            'username' => $account->username,
            'password' => $account->password,
            'pin'      => $account->pin,
        ]);

        if ($model->db->getTransactionStatus()) {
            $id_sys_user = $model->connection->getLastInsertId();

            $model->profile->insert([
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
            ]);

            if ($model->db->getTransactionStatus()) {

                $model->role->insert([
                    'id_sys_user'             => $id_sys_user,
                    'id_sys_module_user_role' => $account->role,
                ]);

                if ($model->db->getTransactionStatus()) {
                    $model->db->transactionCommit();
                }
            }
        }

        $model->db->transactionRollBack();

        return false;
    }

    // ------------------------------------------------------------------------

    public function update(Account $account)
    {
        return models('users')->updateAccount($account);
    }
}