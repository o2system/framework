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

// ------------------------------------------------------------------------

use O2System\Framework\Http\Controllers\Restful;
// ------------------------------------------------------------------------
/**
 * Class Actions
 * @package O2System\Framework\Http\Controllers\System\Users
 */
class Actions extends Restful
{
    /**
     * Actions::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Users\Actions';

    // ------------------------------------------------------------------------
    /**
     * Actions::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [
        'id_sys_user' => 'required|integer',
        'action' => 'optional',
        'role' => 'required',
        'step' => 'optional',
        'reference_id' => 'required|integer',
        'reference_model' => 'required'
    ];

    // ------------------------------------------------------------------------
    
    /**
     * Actions::$createValidationCustomErrors
     *
     * @var array
     */
    public $createValidationCustomErrors = [
        'id_sys_user' => [
            'required' => 'System User cannot be empty!',
            'integer' => 'System User data must be an integer'
        ],
        'role' => ['required' => 'User Action role cannot be empty!'],
        'reference_id' => [
            'required' => 'User Action reference cannot be empty!',
            'integer' => 'System User data must be an integer'
        ],
        'reference_model' => ['required' => 'User Action reference model cannot be empty!'],
    ];

    // ------------------------------------------------------------------------

    /**
     * Actions::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'id_sys_user' => 'required|integer',
        'action' => 'optional',
        'role' => 'required',
        'step' => 'optional',
        'reference_id' => 'required|integer',
        'reference_model' => 'required'
    ];

    // ------------------------------------------------------------------------

    /**
     * Actions::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Action id cannot be empty!',
            'integer' => 'Action id data must be an integer'
        ],
        'id_sys_user' => [
            'required' => 'System User cannot be empty!',
            'integer' => 'System User data must be an integer'
        ],
        'role' => ['required' => 'User Action role cannot be empty!'],
        'reference_id' => [
            'required' => 'User Action reference cannot be empty!',
            'integer' => 'System User data must be an integer'
        ],
        'reference_model' => ['required' => 'User Action reference model cannot be empty!'],
    ];

    // ------------------------------------------------------------------------

    /**
     * Actions::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
        'id_sys_user' => 'optional|integer',
    ];

    // ------------------------------------------------------------------------

    /**
     * Actions::$getValidationCustomErrors
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
     * Actions::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
        'action_model',
    ];
}