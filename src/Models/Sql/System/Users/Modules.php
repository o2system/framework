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

namespace O2System\Framework\Models\Sql\System\Users;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\System;
use O2System\Framework\Models\Sql\Model;

/**
 * Class Modules
 * @package O2System\Framework\Models\Sql\System\Users
 */
class Modules extends Model
{
    /**
     * Modules::$table
     *
     * @var string
     */
    public $table = 'sys_users_modules';

    /**
     * Modules::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        'module',
        'role'
    ];

    // ------------------------------------------------------------------------
    /**
     * Modules::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [
        'id_sys_user' => 'required|integer',
        'id_sys_module' => 'required|integer',
        'id_sys_module_role' => 'required|integer',
    ];

    // ------------------------------------------------------------------------
    /**
     * Modules::$createValidationCustomErrors
     *
     * @var array
     */
    public $createValidationCustomErrors = [
        'id_sys_user' => [
            'required' => 'System User cannot be empty!',
            'integer' => 'System User data must be an integer'
        ],
        'id_sys_module' => [
            'required' => 'System module cannot be empty!',
            'integer' => 'System module data must be an integer'
        ],
        'id_sys_module_role' => [
            'required' => 'System module role cannot be empty!',
            'integer' => 'System module role data must be an integer'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Modules::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'id_sys_user' => 'required|integer',
    ];

    // ------------------------------------------------------------------------

    /**
     * Modules::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Module id cannot be empty!',
            'integer' => 'Module id data must be an integer'
        ],
        'id_sys_user' => [
            'required' => 'System User cannot be empty!',
            'integer' => 'System User data must be an integer'
        ],
        'id_sys_module' => [
            'required' => 'System module cannot be empty!',
            'integer' => 'System module data must be an integer'
        ],
        'id_sys_module_role' => [
            'required' => 'System module role cannot be empty!',
            'integer' => 'System module role data must be an integer'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Modules::user
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function user()
    {
        models(System\Users::class)->hideColumns = [
            'password',
            'pin',
            'sso'
        ];
        return $this->belongsTo(System\Users::class, 'id_sys_user');
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::module
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function module()
    {
        return $this->belongsTo(System\Modules::class, 'id_sys_module');
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::role
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function role()
    {
        return $this->belongsTo(System\Modules\Roles::class, 'id_sys_module_role');
    }
}
