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
use O2System\Framework\Models\Sql\Traits\MetadataTrait;

/**
 * Class Users
 * @package O2System\Framework\Models\Sql\System
 */
class Users extends Model
{
    /**
     * Users::$table
     *
     * @var string
     */
    public $table = 'sys_users';

    /**
     * Users::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        'profile',
        'type'
    ];

    /**
     * Users::$hideColumns
     *
     * @var array
     */
    public $hideColumns = [];

    // ------------------------------------------------------------------------

    /**
     * Users::beforeInsertOrUpdate
     *
     * @param array $sets
     *
     * @return void
     */
    protected function beforeInsert(array &$sets)
    {
        if (key_exists('password_confirm', $sets)) {
            if ($sets[ 'password_confirm' ] !== $sets[ 'password' ]) {
                session()->setFlash('danger', 'password not equal');

                return;
            }
            unset($sets[ 'password_confirm' ]);
        }

//        $sets['password'] = services('user')->passwordHash($sets['password']);

        //if has exits email
        if ($this->hasExist('email', $sets[ 'email' ])) {
//            session()->setFlash('danger', 'email has exist');
            return;
        }
        //if has msisdn

        if ($this->hasExist('msisdn', $sets[ 'msisdn' ])) {
//            session()->setFlash('danger', 'number phone has exist');
            return;
        }

        //if has username

        if ($this->hasExist('username', $sets[ 'username' ])) {
//            session()->setFlash('danger', 'username has exist');
            return;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Users::modules
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function modules()
    {
        return $this->hasManyThrough(Modules::class, Modules\Users::class);
    }

    // ------------------------------------------------------------------------

    /**
     * Users::hasExist
     *
     * @param $field
     * @param $value
     *
     * @return bool
     */
    protected function hasExist($field, $value)
    {
        if (count($this->findWhere([
            $field => $value,
        ]))) {
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Users::profile
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function profile()
    {
        if (globals()->has('module')) {
            if ( ! empty(globals()->module->user->profile)) {
                return globals()->module->user->profile;
            }
        }

        models(Users\Profiles::class)->hideColumns = [
            'id_sys_user'
        ];

        return $this->hasOne(Users\Profiles::class, 'id_sys_user');
    }
    // ------------------------------------------------------------------------

    /**
     * Users::type
     *
     * @return string
     */
    public function type()
    {
        return 'PEOPLE';
    }

    // ------------------------------------------------------------------------

    /**
     * Users::tags
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function tags()
    {
        return $this->hasMany(Users\Tags::class);
    }
    // ------------------------------------------------------------------------

    /**
     * Users::actions
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function actions()
    {
        return $this->hasMany(Users\Actions::class, 'id_sys_user');
    }

    // ------------------------------------------------------------------------

    /**
     * Users::storage
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function storage()
    {
        return $this->morphOne(Storage::class, 'ownership');
    }

    
}