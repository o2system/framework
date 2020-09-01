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

use App\Models\People;
use O2System\Cache\Item;
use O2System\Framework\Services\Email;
use O2System\Security\Generators\Token;
use O2System\Framework\Models\Sql\System\Sessions;
use O2system\Framework\Services\Session;
use O2System\Framework\Models\Sql\System\Users;
use O2System\Kernel\DataStructures\Input\Abstracts\AbstractInput;
use O2System\Security\Authentication\JsonWebToken;
use O2System\Security\Authentication\User;
use O2System\Security\Authentication\User\Account;
use O2System\Security\Authentication\User\Role;
use O2System\Spl\DataStructures\SplArrayObject;
use O2System\Spl\DataStructures\SplArrayStorage;
use O2System\Spl\Traits\Collectors\ErrorCollectorTrait;

/**
 * Class AccessControl
 * @package O2System\Framework\Services
 */
class AccessControl extends User
{
    use ErrorCollectorTrait;

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

        models()->load(Users::class, 'users');
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
        if ($account = $this->find($username)) {
            if ($this->passwordVerify($password, $account->password)) {
                if ($this->passwordRehash($password)) {
                    $update = new SplArrayStorage();
                    $update->append([
                        'password' => $this->passwordHash($password),
                    ]);

                    models(Users::class)->update($update, [
                        'id' => $account->id
                    ]);
                }

                return $this->forceLogin($account);
            }
        }

        $this->attempt();

        $this->addError(__LINE__, 'AUTHENTICATE_FAILED');

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * User::register
     *
     * @author Gemblue
     * @param  AbstractInput $data
     * @return mixed
     */
    public function register($data)
    {
        // Init
        $pin = Token::generate(20);

        // Is email exist before?
        models(Users::class)->qb
            ->where('username', $data['username'])
            ->orWhere('email', $data['email'])
            ->orWhere('msisdn', $data['msisdn'])
            ->limit(1);

        if (($result = models(Users::class)->get())->count()) {
            $this->addError(__LINE__, 'USER_ALREADY_EXISTS');

            return false;
        }

        /** Let's insert to master account */
        $register = models(Users::class)->insert(new SplArrayStorage([
            'fullname' => $data['fullname'],
            'email' => $data['email'],
            'msisdn' => $data['msisdn'],
            'username' => $data['username'],
            'password' => $this->passwordHash($data['password']),
            'password_confirm' => $data['password_confirm'],
            'pin' => $pin,
            'record_status' => 'DRAFT',
            'record_create_timestamp' => date('Y-m-d H:i:s')
        ]));
        
        if ($register) {
           
            /** Send Email */
            $email = new Email();
            $email->subject('Registration - Please Confirm Your Email');
            $email->from('noreply@gocart.com', 'noreply@gocart.com');
            $email->to('gocart-a7925d@inbox.mailtrap.io');
            $email->template('email/registration', [
                'name' => $data['fullname'],
                'link' => base_url('users/register/confirm/' . $pin)
            ]);
            
            if ($email->send()) {
                return true;
            }

            return false;
        }
        
        $this->addError(__LINE__, 'USER_REGISTRATION_FAILED');

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
    public function find($username, $column = 'username')
    {
        if (is_numeric($username)) {
            $column = 'id';
        } elseif (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $column = 'email';
        } elseif (preg_match($this->config['msisdnRegex'], $username)) {
            $column = 'msisdn';
        }

        if ($user = models(Users::class)->findWhere([
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
        if (session()->has('account')) {
            return true;
        }

        if ($this->tokenOn()) {
            return true;
        }

        if ($this->signOn()) {
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
        if (!is_object($username)) {
            $account = $this->find($username, $column);
        } else {
            $account = $username;
        }

        if (isset($account)) {
            $account = $account->getArrayCopy();

            foreach ($account as $key => $value) {
                if (strpos($key, 'record') !== false) {
                    unset($account[$key]);
                } elseif (in_array($key, ['password', 'pin', 'token', 'sso'])) {
                    unset($account[$key]);
                }
            }

            $jwt = new JsonWebToken();

            $jwt = $jwt->encode(array_merge($account, [
                'iat' => $timestamp = timestamp(),
                'exp' => $expires = timestamp(time() + config()->session['lifetime'])
            ]));

            $userAgent = input()->server('HTTP_USER_AGENT');
            $ssid = substr(md5(json_encode($account) . $userAgent), -6, 10);

            $session = new SplArrayStorage();
            $session->append([
                'id_session' => session_id(),
                'ssid' => $ssid,
                'jwt' => $jwt,
                'timestamp' => $timestamp,
                'expires' => $expires,
                'useragent' => $userAgent,
                'ownership_id' => $account['id'],
                'ownership_model' => Users::class
            ]);

            if (models(Sessions::class)->insertOrUpdate($session, [
                'id_session' => session_id()
            ])) {
                session()->offsetSet('ssid', $ssid);
                session()->offsetSet('jwt', $jwt);
                session()->offsetSet('expires', $expires);
                session()->offsetSet('timestamp', $timestamp);
                session()->offsetSet('account', new Account($account));

                return true;
            }
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
                            'id_sys_module_user' => $account->user->moduleUser->id,
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
                            'id_sys_module_role' => $account->user->role->id,
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
                            'id_sys_module_user' => $account->user->moduleUser->id,
                        ])) {
                            if ($authority->first()->permission === 'WRITE') {
                                return true;
                            }
                        }

                        if ($authority = $model->segments->authorities->roles->findWhere([
                            'id_sys_module_segment' => $segment->id,
                            'id_sys_module_role' => $account->user->role->id,
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
     * AccessControl::tokenOn
     */
    public function tokenOn()
    {
        if (false !== ($bearerToken = input()->bearerToken())) {
            $payload = (new JsonWebToken())->decode($bearerToken);
        } elseif (null !== ($cookieJWT = get_cookie('jwt'))) {
            $payload = (new JsonWebToken())->decode($cookieJWT);
            delete_cookie('jwt');
        }

        if (!empty($payload)) {
            $this->forceLogin($payload['username']);
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * AccessControll::tokenOff
     */
    public function tokenOff()
    {
        if ($cookieSSID = get_cookie('jwt')) {
            delete_cookie('jwt');
        }
    }

    // ------------------------------------------------------------------------

    /**
     * AccessControl::logout
     */
    public function logout()
    {
        if(services()->has('session')) {
            session()->offsetUnset('account');
            session()->destroy();
        } else {
            $session = new Session(config('session', true));
            $session->setLogger(services()->get('logger'));

            $session->start();

            $session->offsetUnset('account');
            $session->destroy();
        }

        unset($_SESSION['account']);

        foreach($_COOKIE as $key => $value) {
            unset($_COOKIE[$key]);
        }

        $this->signOff();
        $this->tokenOff();
    }

    // ------------------------------------------------------------------------

    /**
     * AccessControl::signOn
     *
     * @return bool
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function signOn()
    {
        if (null !== ($cookieSSID = get_cookie('ssid'))) {
            if ($session = models('users')->sessions->findWhere([
                'ssid' => $cookieSSID
            ], 1)) {
                if (timestamp() >= strtotime($session->expires)) {
                    delete_cookie('ssid');
                    return false;
                }

                $account = (new JsonWebToken())->decode($session->jwt);

                return $this->forceLogin($account['username']);
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * AccessControl::signOff
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function signOff()
    {
        if ($cookieSSID = get_cookie('ssid')) {
            delete_cookie('ssid');
        }
    }

    // ------------------------------------------------------------------------

    /**
     * AccessControl::getIframeCode
     *
     * @return string
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getIframeCode()
    {
        if ($this->signedOn() && $this->loggedIn() === false) {
            return '<iframe id="sign-on-iframe" width="1" height="1" src="' . rtrim($this->config['sso']['server'],
                    '/') . '" style="display: none; visibility: hidden;"></iframe>';
        }

        return '';
    }
}