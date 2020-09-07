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

namespace O2System\Framework\Models\Sql\System;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\SettingsTrait;
use O2System\Spl\DataStructures\SplArrayStorage;

/**
 * Class Users
 * @package O2System\Framework\Models\Sql\System
 */
class Users extends Model
{
    use SettingsTrait;

    /**
     * Users::$table
     *
     * @var string
     */
    public $table = 'sys_users';

    /**
     * Users::$fillableColumns
     *
     * @var array
     */
    public $fillableColumns = [
        'email',
        'msisdn',
        'username',
        'password',
        'pin',
        'record_status',
        'record_insert_timestamp'
    ];

    /**
     * Users::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        'profile'
    ];


    /**
     * Users::$insertValidationRules
     *
     * @var array
     */
    public $insertValidationRules = [
        'email' => 'required|email',
        'msisdn' => 'required|msisdn[0]',
        'username' => 'required',
        'password' => 'required|password',
        'pin'   => 'required',
    ];

    // ------------------------------------------------------------------------

    /**
     * Users::$insertValidationCustomErrors
     *
     * @var array
     */
    public $insertValidationCustomErrors = [
        'email' => [
            'required' => 'Email cannot be empty!'
        ],
        'msisdn' => [
            'required' => 'msisdn cannot be empty!'
        ],
        'username' => [
            'required' => 'Username cannot be empty!'
        ],
        'password' => [
            'required' => 'Password cannot be empty!'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Users::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'email' => 'required|email',
        'msisdn' => 'required|msisdn[0]',
        'username' => 'required',
        'password' => 'required|password',
        'pin'   => 'required',
    ];

    // ------------------------------------------------------------------------

    /**
     * Users::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'User id cannot be empty!',
            'integer' => 'User id data must be an integer'
        ],
        'email' => [
            'required' => 'Email cannot be empty!'
        ],
        'msisdn' => [
            'required' => 'msisdn cannot be empty!'
        ],
        'username' => [
            'required' => 'Username cannot be empty!'
        ],
        'password' => [
            'required' => 'Password cannot be empty!'
        ],
        'pin'   =>  [
            'required' => 'Pin cannot be empty!'
        ],
        'sso'   => [
            'required' => 'Sso cannot be empty!'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Users::beforeInsertOrUpdate
     *
     * @param array $sets
     *
     * @return bool
     */
    protected function beforeInsert(SplArrayStorage &$sets)
    {
        if (key_exists('password_confirm', $sets)) {
            if ($sets[ 'password_confirm' ] !== $sets[ 'password' ]) {
                if (services()->has('session') and $this->flashMessage) {
                    session()->setFlash('danger', language('E_PASSWORD_NOT_EQUAL'));
                }

                return false;
            }

            unset($sets[ 'password_confirm' ]);
        }

        if (count($this->findWhere([
            'email' => $sets[ 'email' ],
        ]))) {
            if (services()->has('session') and $this->flashMessage) {
                session()->setFlash('danger', language('E_EMAIL_EXISTS'));
            }

            return false;
        }

        if (count($this->findWhere([
            'msisdn' => $sets[ 'msisdn' ],
        ]))) {
            if (services()->has('session') and $this->flashMessage) {
                session()->setFlash('danger', language('E_MSISDN_EXISTS'));
            }

            return false;
        }

        if (count($this->findWhere([
            'username' => $sets[ 'username' ],
        ]))) {
            if (services()->has('session') and $this->flashMessage) {
                session()->setFlash('danger', language('E_USERNAME_EXISTS'));
            }

            return false;
        }

        return true;
    }

    // ------------------------------------------------------------------------

    /**
     * Users::afterInsert
     *
     * @param SplArrayStorage $sets
     *
     * @return void
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     */
    protected function afterInsert(SplArrayStorage &$sets)
    {
        // Insert to people.
        models(People::class)->insert(new SplArrayStorage([
            'fullname'  =>  $sets['fullname'],
            'gender'    => 'MALE'
        ]));
    }

    // ------------------------------------------------------------------------

    /**
     * Users::modules
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function modules()
    {
        return $this->hasManyThrough(Modules::class, Users\Modules::class);
    }

    // ------------------------------------------------------------------------

    /**
     * Users::profile
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function profile()
    {
        return $this->morphOneThrough(People::class, Relationships::class, 'relation');
    }
    // ------------------------------------------------------------------------

    /**
     * Users::avatar
     *
     * @return string
     */
    public function avatar()
    {
        return 'https://avatars.dicebear.com/v2/initials/' . preg_replace('/[^a-z0-9 _.-]+/i', '', $this->row->username) . '.svg';
    }
}
