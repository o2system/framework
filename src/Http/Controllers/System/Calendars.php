<?php


namespace O2System\Framework\Http\Controllers\System;


use O2System\Framework\Http\Controllers\Restful;

class Calendars extends Restful
{
    /**
     * Calendars::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Calendars';

    // ------------------------------------------------------------------------
    /**
     * Calendars::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [
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
    public $createValidationCustomErrors = [
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
     * Calendars::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Calendars::$getValidationCustomErrors
     *
     * @var array
     */
    public $getValidationCustomErrors = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Calendars::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
        'datetime_start',
        'datetime_end',
        'record_type'
    ];
}