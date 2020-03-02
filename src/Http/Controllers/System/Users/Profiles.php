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

/**
 * Class Profiles
 * @package O2System\Framework\Http\Controllers\System\Users
 */
class Profiles extends Restful
{
    /**
     * Profiles::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Users\Profiles';

    // ------------------------------------------------------------------------
    /**
     * Profiles::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [
        'id_sys_user' => 'required|integer',
        'fullname' => 'required',
        'avatar' => 'optional',
        'cover' => 'optional',
        'gender' => 'required|listed[MALE, FEMALE]',
    ];

    // ------------------------------------------------------------------------
    /**
     * Profiles::$createValidationCustomErrors
     *
     * @var array
     */
    public $createValidationCustomErrors = [
        'id_sys_user' => [
            'required' => 'System User cannot be empty!',
            'integer' => 'System User data must be an integer'
        ],
        'fullname' => ['required' => 'User Profile fullname cannot be empty!'],
        'gender' => [
            'required' => 'User profile  cannot be empty!',
            'listed' => 'User profile gender must be listed: MALE or FEMALE'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Profiles::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'id_sys_user' => 'required|integer',
        'fullname' => 'required',
        'avatar' => 'optional',
        'cover' => 'optional',
        'gender' => 'required|listed[MALE, FEMALE]',
    ];

    // ------------------------------------------------------------------------

    /**
     * Profiles::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'User id cannot be empty!',
            'integer' => 'User id data must be an integer'
        ],
        'id_sys_user' => [
            'required' => 'System User cannot be empty!',
            'integer' => 'System User data must be an integer'
        ],
        'fullname' => ['required' => 'User Profile fullname cannot be empty!'],
        'gender' => [
            'required' => 'User gender cannot be empty!',
            'listed' => 'User profile gender must be listed: MALE or FEMALE'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Profiles::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
        'id_sys_user' => 'optional|integer',
    ];

    // ------------------------------------------------------------------------

    /**
     * Profiles::$getValidationCustomErrors
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
     * Profiles::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
        'fullname',
    ];
}