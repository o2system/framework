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
namespace O2System\Framework\Http\Controllers\System\Users\Sessions;

// ------------------------------------------------------------------------
use O2System\Framework\Http\Controllers\Restful;
// ------------------------------------------------------------------------
/**
 * Class Trails
 * @package O2System\Framework\Http\Controllers\System\Users\Sessions
 */
class Trails extends Restful
{
    /**
     * Trains::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Users\Sessions\Trails';

    // ------------------------------------------------------------------------
    /**
     * Trails::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [
        'id_sys_user_session' => 'required|integer',
        'url' => 'optional',
        'type' => 'optional',
        'session' => 'optional',
        'ip_address' => 'optional',
        'status' => 'optional|listed[GRANTED,DENIED]',
        'time_start' => 'optional|date[Y-m-d h:i:s]',
        'time_end' => 'optional|date[Y-m-d h:i:s]',
        'metadata' => 'optional',

    ];

    // ------------------------------------------------------------------------
    /**
     * Trails::$createValidationCustomErrors
     *
     * @var array
     */
    public $createValidationCustomErrors = [
        'id_sys_user' => [
            'required' => 'System user session cannot be empty!',
            'integer' => 'System user session data must be an integer'
        ],
        'status' => [
            'listed' => 'User session trail record type must be listed: GRANTED,DENIED'
        ],
        'time_start' => [
            'date' => 'User session trail date format must be Y-m-d H:i:s'
        ],
        'time_end' => [
            'date' => 'User session trail date format must be Y-m-d H:i:s'
        ],

    ];

    // ------------------------------------------------------------------------

    /**
     * Trails::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'id_sys_user' => 'required|integer',
        'url' => 'optional',
        'type' => 'optional',
        'session' => 'optional',
        'ip_address' => 'optional',
        'status' => 'optional|listed[GRANTED,DENIED]',
        'time_start' => 'optional|date[Y-m-d h:i:s]',
        'time_end' => 'optional|date[Y-m-d h:i:s]',
        'metadata' => 'optional',
    ];

    // ------------------------------------------------------------------------

    /**
     * Trails::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Session trail id cannot be empty!',
            'integer' => 'Session trail id data must be an integer'
        ],
        'id_sys_user_session' => [
            'required' => 'System user session cannot be empty!',
            'integer' => 'System user session data must be an integer'
        ],
        'status' => [
            'listed' => 'User session trail record type must be listed: GRANTED,DENIED'
        ],
        'time_start' => [
            'date' => 'User session trail date format must be Y-m-d H:i:s'
        ],
        'time_end' => [
            'date' => 'User session trail date format must be Y-m-d H:i:s'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Trails::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
        'id_sys_user' => 'optional|integer',
    ];

    // ------------------------------------------------------------------------

    /**
     * Trails::$getValidationCustomErrors
     *
     * @var array
     */
    public $getValidationCustomErrors = [
        'id_sys_user' => [
            'integer' => 'System user session data must be an integer'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Trails::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
    ];

}