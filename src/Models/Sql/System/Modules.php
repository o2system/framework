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
use O2System\Framework\Models\Sql\Traits\HierarchicalTrait;
use O2System\Framework\Models\Sql\Traits\SettingsTrait;

/**
 * Class Modules
 * @package O2System\Framework\Models\Sql\System\Models
 */
class Modules extends Model
{
    use SettingsTrait;
    use HierarchicalTrait;

    /**
     * Modules::$table
     *
     * @var string
     */
    public $table = 'sys_modules';

    /**
     * Modules::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        'settings'
    ];

    // ------------------------------------------------------------------------

    /**
     * Modules::languages
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function languages()
    {
        return $this->hasMany(Modules\Languages::class);
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::menus
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function menus()
    {
        return $this->hasMany(Modules\Menus::class);
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::role
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function role()
    {
        if(session()->has('account')) {
            if($result = $this->qb
                ->select('sys_modules_roles.*')
                ->from('sys_modules')
                ->join('sys_users_modules', 'sys_users_modules.id_sys_module = sys_modules.id')
                ->join('sys_modules_roles', 'sys_modules_roles.id = sys_users_modules.id_sys_module_role')
                ->where([
                    'sys_users_modules.id_sys_user' => session()->account->id
                ])
                ->get(1)
            ) {
                if($result->count()) {
                    return $result->first();
                }
            }
        }
        
        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::roles
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function roles()
    {
        return $this->hasMany(Modules\Roles::class);
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::segments
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function segments()
    {
        return $this->hasMany(Modules\Segments::class);
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::user
     *
     * @return bool|mixed|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function user()
    {
        if(globals()->has('account')) {
            $this->qb->where('sys_modules_users.id_sys_user', globals()->account->id);

            return $this->hasOne(Modules\Users::class);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::users
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function users()
    {
        return $this->hasMany(Modules\Users::class);
    }
}