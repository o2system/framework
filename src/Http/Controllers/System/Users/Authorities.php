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


namespace O2System\Framework\Http\Controllers\System\Users;


use O2System\Framework\Http\Controllers\Restful;

/**
 * Class Authorities
 * @package O2System\Framework\Http\Controllers\System\Users
 */
class Authorities extends Restful
{
    /**
     * Authorities::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Users\Authorities';

    // ------------------------------------------------------------------------
    /**
     * Authorities::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [
        'id_sys_user' => 'required|integer',
        'id_sys_module_segment' => 'required|integer',
        'permission' => 'required|listed[DENIED,GRANTED,WRITE]'
    ];

    // ------------------------------------------------------------------------
    /**
     * Authorities::$createValidationCustomErrors
     *
     * @var array
     */
    public $createValidationCustomErrors = [
        'id_sys_user' => [
            'required' => 'System User cannot be empty!',
            'integer' => 'System User data must be an integer'
        ],
        'id_sys_module_segment' => [
            'required' => 'System User module segment cannot be empty!',
            'integer' => 'System User module segment data cannot be empty!',
        ],
        'permission' => [
            'required' => 'System User authorities cannot be empty!',
            'listed' => 'System User authorities data must be listed : DENIED,GRANTED,WRITE'
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
        'id_sys_user' => 'required|integer',
        'id_sys_module_segment' => 'required|integer',
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
            'required' => 'Comment id cannot be empty!',
            'integer' => 'Comment id data must be an integer'
        ],
        'id_sys_user' => [
            'required' => 'System User cannot be empty!',
            'integer' => 'System User data must be an integer'
        ],
        'id_sys_module_segment' => [
            'required' => 'System User module segment cannot be empty!',
            'integer' => 'System User module segment data cannot be empty!',
        ],
        'permission' => [
            'required' => 'System User authorities cannot be empty!',
            'listed' => 'System User authorities data must be listed : DENIED,GRANTED,WRITE'
        ]
    ];

    // ------------------------------------------------------------------------

    /**
     * Authorities::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
        'id_sys_user' => 'optional|integer',
    ];

    // ------------------------------------------------------------------------

    /**
     * Authorities::$getValidationCustomErrors
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
     * Authorities::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
        'permission'
    ];

}