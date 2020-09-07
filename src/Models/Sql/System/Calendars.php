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
        'ownership_id' => 'required',
        'ownership_model' => 'required',
        'start_date' => 'optional|date[Y-m-d]',
        'start_time' => 'optional|date[h:i:s]',
        'end_date' => 'optional|date[Y-m-d]',
        'end_time' => 'optional|date[h:i:s]',
        'repeat_minute' => 'optional',
        'repeat_hour' => 'optional',
        'repeat_day' => 'optional',
        'repeat_date' => 'optional',
        'repeat_month' => 'optional',
        'repeat_year' => 'optional',
        'repeat_until' => 'optional',
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
        ],
        'ownership_model' => [
            'required' => 'Ownership model cannot be empty!'
        ],
        'start_date' => [
            'date' => 'Calendar datetime start data must be an date Y-m-d'
        ],
        'start_time' => [
            'date' => 'Calendar datetime start time must be an date Y-m-d h:i:s'
        ],
        'end_date' => [
            'date' => 'Calendar datetime end date must be an date Y-m-d'
        ],
        'end_time' => [
            'date' => 'Calendar datetime end time data must be an date h:i:s'
        ],
        'record_type' => [
            'listed' => 'Calendar record type data must be listed: HOLIDAY,BIRTHDAY,LEAVE,EVENT,MEETING,PROJECT,APPOINTMENT,REMINDER,TASK,GOAL,NEWS,ANNOUNCEMENT,SCHEDULE,CAREER,CONTRACT,MAINTENANCE,TRAINING'
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
        'ownership_id' => 'required',
        'ownership_model' => 'required',
        'start_date' => 'optional|date[Y-m-d]',
        'start_time' => 'optional|date[h:i:s]',
        'end_date' => 'optional|date[Y-m-d]',
        'end_time' => 'optional|date[h:i:s]',
        'repeat_minute' => 'optional',
        'repeat_hour' => 'optional',
        'repeat_day' => 'optional',
        'repeat_date' => 'optional',
        'repeat_month' => 'optional',
        'repeat_year' => 'optional',
        'repeat_until' => 'optional',
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
        ],
        'ownership_model' => [
            'required' => 'Ownership model cannot be empty!'
        ],
        'start_date' => [
            'date' => 'Calendar datetime start data must be an date Y-m-d'
        ],
        'start_time' => [
            'date' => 'Calendar datetime start time must be an date Y-m-d h:i:s'
        ],
        'end_date' => [
            'date' => 'Calendar datetime end date must be an date Y-m-d'
        ],
        'end_time' => [
            'date' => 'Calendar datetime end time data must be an date h:i:s'
        ],
        'record_type' => [
            'listed' => 'Calendar record type data must be listed: HOLIDAY,BIRTHDAY,LEAVE,EVENT,MEETING,PROJECT,APPOINTMENT,REMINDER,TASK,GOAL,NEWS,ANNOUNCEMENT,SCHEDULE,CAREER,CONTRACT,MAINTENANCE,TRAINING'
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
