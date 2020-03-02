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
 * Class Notifications
 * @package O2System\Framework\Http\Controllers\System\Users
 */
class Notifications extends Restful
{
    /**
     * Notifications::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Users\Notifications';

    // ------------------------------------------------------------------------
    /**
     * Notifications::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [
        'id_sys_user' => 'required|integer',
        'id_sys_user_sender' => 'required|integer',
        'notification_id' => 'required|integer',
        'notification_model' => 'required',
        'message' => 'required',
        'timestamp' => 'required',
        'metadata' => 'optional',
        'status' => 'optional|listed[UNSEEN,SEEN,UNREAD,READ]',
    ];

    // ------------------------------------------------------------------------
    /**
     * Notifications::$createValidationCustomErrors
     *
     * @var array
     */
    public $createValidationCustomErrors = [
        'id_sys_user' => [
            'required' => 'System User cannot be empty!',
            'integer' => 'System User data must be an integer'
        ],
        'id_sys_user_sender' => [
            'required' => 'System User sender  cannot be empty!',
            'integer' => 'System User sender data must be an integer'
        ],
        'reference_id' => [
            'required' => 'System User reference id cannot be empty!',
            'integer' => 'System User reference id data must be an integer'
        ],
        'message' => [
            'required' => 'System User notification message cannot be empty!',
            'integer' => 'System User notification message data must be an integer'
        ],
        'timestamp' => [
            'required' => 'System User notification timestamp cannot be empty!'
        ],
        'status' => [
            'listed' => 'Calendar record type must be listed: UNSEEN,SEEN,UNREAD,READ'
        ],

    ];

    // ------------------------------------------------------------------------

    /**
     * Notifications::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'id_sys_user' => 'required|integer',
        'id_sys_user_sender' => 'required|integer',
        'notification_id' => 'required|integer',
        'notification_model' => 'required',
        'message' => 'required',
        'timestamp' => 'required',
        'metadata' => 'optional',
        'status' => 'optional|listed[UNSEEN,SEEN,UNREAD,READ]',
    ];

    // ------------------------------------------------------------------------

    /**
     * Notifications::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Notification id cannot be empty!',
            'integer' => 'Notification id data must be an integer'
        ],
        'id_sys_user' => [
            'required' => 'System User cannot be empty!',
            'integer' => 'System User data must be an integer'
        ],
        'id_sys_user_sender' => [
            'required' => 'System User sender  cannot be empty!',
            'integer' => 'System User sender data must be an integer'
        ],
        'reference_id' => [
            'required' => 'System User reference id cannot be empty!',
            'integer' => 'System User reference id data must be an integer'
        ],
        'message' => [
            'required' => 'System User notification message cannot be empty!',
            'integer' => 'System User notification message data must be an integer'
        ],
        'timestamp' => [
            'required' => 'System User notification timestamp cannot be empty!'
        ],
        'status' => [
            'listed' => 'Calendar record type must be listed: UNSEEN,SEEN,UNREAD,READ'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Notifications::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Notifications::$getValidationCustomErrors
     *
     * @var array
     */
    public $getValidationCustomErrors = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Notifications::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
        'message',
        'timestamp',
        'status',
        'metadata'
    ];

}