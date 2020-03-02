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
namespace O2System\Framework\Http\Controllers\System\Users;
// ------------------------------------------------------------------------

use O2System\Framework\Http\Controllers\Restful;
// ------------------------------------------------------------------------
/**
 * Class Modules
 * @package O2System\Framework\Http\Controllers\System\Users
 */
class Modules extends Restful
{
    /**
     * Modules::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Users\Modules';

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
     * Modules::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
        'id_sys_user' => 'optional|integer',
    ];

    // ------------------------------------------------------------------------

    /**
     * Modules::$getValidationCustomErrors
     *
     * @var array
     */
    public $getValidationCustomErrors = [
        'id_sys_user' => [
            'integer' => 'System User data must be an integer'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Modules::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
    ];

}