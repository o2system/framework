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
 * Class Sessions
 * @package O2System\Framework\Http\Controllers\System\Users
 */
class Sessions extends Restful
{
    /**
     * Sessions::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Users\Sessions';

    // ------------------------------------------------------------------------
    /**
     * Sessions::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [
        'id_sys_user' => 'required|integer',
        'token' => 'required',
        'payload' => 'required',
        'timestamp' => 'required|date[Y-m-d h:i:s]',
        'expires' => 'required|date[Y-m-d h:i:s]',
        'useragent' => 'required'
    ];

    // ------------------------------------------------------------------------
    /**
     * Sessions::$createValidationCustomErrors
     *
     * @var array
     */
    public $createValidationCustomErrors = [
        'id_sys_user' => [
            'required' => 'System User cannot be empty!',
            'integer' => 'System User data must be an integer'
        ],
        'token' => [
            'required' => 'Token cannot be an empty!'
        ],
        'payload' => [
            'required' => 'Payload cannot be an empty!'
        ],
        'timestamp' => [
            'required' => 'Timestamp date cannot be an empty!',
            'date' => 'Timestamp date format must be Y-m-d H:i:s'
        ],
        'expires' => [
            'required' => 'Expired date cannot be an empty!',
            'date' => 'Expired date format must be Y-m-d H:i:s'
        ],
        'useragent' => [
            'required' => 'Useragent cannot be an empty!'
        ],

    ];

    // ------------------------------------------------------------------------

    /**
     * Sessions::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'id_sys_user' => 'required|integer',
        'token' => 'required',
        'payload' => 'required',
        'timestamp' => 'required|date[Y-m-d h:i:s]',
        'expires' => 'required|date[Y-m-d h:i:s]',
        'useragent' => 'required'
    ];

    // ------------------------------------------------------------------------

    /**
     * Sessions::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Session id cannot be empty!',
            'integer' => 'Session id data must be an integer'
        ],
        'id_sys_user' => [
            'required' => 'System User cannot be empty!',
            'integer' => 'System User data must be an integer'
        ],
        'token' => [
            'required' => 'Token cannot be an empty!'
        ],
        'payload' => [
            'required' => 'Payload cannot be an empty!'
        ],
        'timestamp' => [
            'required' => 'Timestamp date cannot be an empty!',
            'date' => 'Timestamp date format must be Y-m-d H:i:s'
        ],
        'expires' => [
            'required' => 'Expired date cannot be an empty!',
            'date' => 'Expired date format must be Y-m-d H:i:s'
        ],
        'useragent' => [
            'required' => 'Useragent cannot be an empty!'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Sessions::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
        'id_sys_user' => 'optional|integer',
    ];

    // ------------------------------------------------------------------------

    /**
     * Sessions::$getValidationCustomErrors
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
     * Sessions::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
        'token',
        'payload',
        'timestamp',
        'expires',
        'useragent'
    ];

}