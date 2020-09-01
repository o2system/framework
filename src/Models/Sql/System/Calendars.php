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

namespace O2System\Framework\Models\Sql\System;


use O2System\Framework\Models\Sql\Model;

/**
 * Class Calendars
 * @package O2System\Framework\Models\Sql\System
 */
class Calendars extends Model
{
    /**
     * Calendars::$table
     *
     * @var string
     */
    public $table = 'sys_calendars';

    /**
     * Calendars::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
    ];

    // ------------------------------------------------------------------------
    /**
     * Calendars::$createValidationRules
     *
     * @var array
     */
    public $insertValidationRules = [
        'ownership_id' => 'required|integer',
        'ownership_model' => 'required',
        'ownership_key' => 'optional',
        'datetime_start' => 'required|date[Y-m-d h:i:s]',
        'datetime_end' => 'optional|date[Y-m-d h:i:s]',
        'record_type' => 'listed[HOLIDAY,BIRTHDAY,LEAVE,EVENT,MEETING,PROJECT,APPOINTMENT,REMINDER,TASK,GOAL,NEWS,ANNOUNCEMENT,SCHEDULE,CAREER,CONTRACT,MAINTENANCE,TRAINING]'
    ];

    // ------------------------------------------------------------------------
    /**
     * Calendars::$createValidationCustomErrors
     *
     * @var array
     */
    public $insertValidationCustomErrors = [
        'ownership_id' => [
            'required' => 'Ownership id cannot be empty!',
            'integer' => 'Ownership id data must be an integer'
        ],
        'ownership_model' => [
            'required' => 'Ownership model cannot be empty!'
        ],
        'datetime_start' => [
            'required' => 'Calendars name cannot be empty!',
            'date' => 'Calendar datetime start data must be an date Y-m-d h:i:s'
        ],
        'datetime_end' => [
            'date' => 'Calendar datetime start data must be an date Y-m-d h:i:s'
        ],
        'record_type' => [
            'listed' => 'Calendar recorrt type data must be listed: HOLIDAY,BIRTHDAY,LEAVE,EVENT,MEETING,PROJECT,APPOINTMENT,REMINDER,TASK,GOAL,NEWS,ANNOUNCEMENT,SCHEDULE,CAREER,CONTRACT,MAINTENANCE,TRAINING'
        ]
    ];

    // ------------------------------------------------------------------------

    /**
     * Calendars::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'ownership_id' => 'required|integer',
        'ownership_model' => 'required',
        'ownership_key' => 'optional',
        'datetime_start' => 'required|date[Y-m-d h:i:s]',
        'datetime_end' => 'optional|date[Y-m-d h:i:s]',
        'record_type' => 'listed[HOLIDAY,BIRTHDAY,LEAVE,EVENT,MEETING,PROJECT,APPOINTMENT,REMINDER,TASK,GOAL,NEWS,ANNOUNCEMENT,SCHEDULE,CAREER,CONTRACT,MAINTENANCE,TRAINING]'
    ];

    // ------------------------------------------------------------------------

    /**
     * Calendars::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Calendars id cannot be empty!',
            'integer' => 'Calendars id data must be an integer'
        ],
        'ownership_id' => [
            'required' => 'Ownership id cannot be empty!',
            'integer' => 'Ownership id data must be an integer'
        ],
        'ownership_model' => [
            'required' => 'Ownership model cannot be empty!'
        ],
        'datetime_start' => [
            'required' => 'Calendars name cannot be empty!',
            'date' => 'Calendar datetime start data must be an date Y-m-d h:i:s'
        ],
        'datetime_end' => [
            'date' => 'Calendar datetime start data must be an date Y-m-d h:i:s'
        ],
        'record_type' => [
            'listed' => 'Calendar recorrt type data must be listed: HOLIDAY,BIRTHDAY,LEAVE,EVENT,MEETING,PROJECT,APPOINTMENT,REMINDER,TASK,GOAL,NEWS,ANNOUNCEMENT,SCHEDULE,CAREER,CONTRACT,MAINTENANCE,TRAINING'
        ]
    ];

    // ------------------------------------------------------------------------

    /**
     * Calendars::ownership
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function ownership()
    {
        return $this->morphTo();
    }
}
