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

use App\Models\People;
use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\SettingsTrait;

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
     * Users::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        'profile'
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
     * @return bool
     */
    protected function beforeInsert(array &$sets)
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
}