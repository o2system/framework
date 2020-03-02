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
namespace O2System\Framework\Http\Controllers\System\Modules;

// ------------------------------------------------------------------------

use O2System\Framework\Http\Controllers\Restful;

/**
 * Class Roles
 * @package O2System\Framework\Http\Controllers\System\Modules
 */
class Roles extends Restful
{
    /**
     * Roles::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Modules\Roles';

    // ------------------------------------------------------------------------
    /**
     * Roles::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [
        'id_sys_module' => 'required|integer',
        'label' => 'required',
        'description' => 'optional',
        'code' => 'required',
    ];

    // ------------------------------------------------------------------------
    /**
     * Roles::$createValidationCustomErrors
     *
     * @var array
     */
    public $createValidationCustomErrors = [
        'id_sys_module' => [
            'required' => 'System Module cannot be empty!',
            'integer' => 'System Module data must be an integer'
        ],
        'label' => ['required' => 'System Module Role label cannot be empty!'],
        'code' => ['required' => 'System Module Role code cannot be empty!'],
    ];

    // ------------------------------------------------------------------------

    /**
     * Roles::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'id_sys_module' => 'required|integer',
        'label' => 'required',
        'description' => 'optional',
        'code' => 'required',
    ];

    // ------------------------------------------------------------------------

    /**
     * Roles::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Role id cannot be empty!',
            'integer' => 'Role id data must be an integer'
        ],
        'id_sys_module' => [
            'required' => 'System Module cannot be empty!',
            'integer' => 'System Module data must be an integer'
        ],
        'label' => ['required' => 'System Module Role label cannot be empty!'],
        'code' => ['required' => 'System Module Role code cannot be empty!'],
    ];

    // ------------------------------------------------------------------------

    /**
     * Roles::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
        'id_sys_module' => 'optional|integer',
    ];

    // ------------------------------------------------------------------------

    /**
     * Roles::$getValidationCustomErrors
     *
     * @var array
     */
    public $getValidationCustomErrors = [
        'id_sys_module' => [
            'integer' => 'System Module data must be an integer'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Roles::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
        'label',
        'code',
    ];
}