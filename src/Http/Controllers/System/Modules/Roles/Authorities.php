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
namespace O2System\Framework\Http\Controllers\System\Roles;


use O2System\Framework\Http\Controllers\Restful;

/**
 * Class Authorities
 * @package O2System\Framework\Http\Controllers\System\Roles
 */
class Authorities extends Restful
{
    /**
     * Authorities::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Modules\Roles\Authorities';

    // ------------------------------------------------------------------------
    /**
     * Authorities::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [
        'id_sys_module_role' => 'required|integer',
        'id_sys_module_endpoint' => 'rquired|integer',
        'permission' => 'required|listed[DENIED,GRANTED,WRITE]'
    ];

    // ------------------------------------------------------------------------
    /**
     * Authorities::$createValidationCustomErrors
     *
     * @var array
     */
    public $createValidationCustomErrors = [
        'id_sys_module' => [
            'required' => 'System Module cannot be empty!',
            'integer' => 'System Module data must be an integer'
        ],
        'id_sys_module_endpoint' => [
            'required' => 'System module enpoint  cannot be empty!',
            'integer' => 'System module enpoint data must be an integer'
        ],
        'permission' => [
            'required' => 'System module role authority cannot be empty!',
            'listed' => 'System module role athority permission must be listed: DENIED,GRANTED,WRITE'
        ]

    ];

    // ------------------------------------------------------------------------

    /**
     * Authorities::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'id_sys_module_role' => 'required|integer',
        'id_sys_module_endpoint' => 'rquired|integer',
        'permission' => 'required|listed[DENIED,GRANTED,WRITE]'
    ];

    // ------------------------------------------------------------------------

    /**
     * Authorities::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Setting id cannot be empty!',
            'integer' => 'Setting id data must be an integer'
        ],
        'id_sys_module' => [
            'required' => 'System Module cannot be empty!',
            'integer' => 'System Module data must be an integer'
        ],
        'id_sys_module_endpoint' => [
            'required' => 'System module enpoint  cannot be empty!',
            'integer' => 'System module enpoint data must be an integer'
        ],
        'permission' => [
            'required' => 'System module role authority cannot be empty!',
            'listed' => 'System module role athority permission must be listed: DENIED,GRANTED,WRITE'
        ]
    ];

    // ------------------------------------------------------------------------

    /**
     * Authorities::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Authorities::$getValidationCustomErrors
     *
     * @var array
     */
    public $getValidationCustomErrors = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Authorities::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
    ];

}