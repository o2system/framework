<?php


namespace O2System\Framework\Models\Sql\System;


use O2System\Framework\Models\Sql\Model;

/**
 * Class Repeaters
 * @package O2System\Framework\Models\Sql\System
 */
class Repeaters extends Model
{
    /**
     * @var string
     */
    public $table = 'sys_repeaters';

    // ------------------------------------------------------------------------
    /**
     * Repeaters::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [
        'repeatable_id' => 'required|integer',
        'repeatable_model' => 'required',
        'repeat_minute' => 'optional',
        'repeat_hour' => 'optional',
        'repeat_day' => 'optional',
        'repeat_date' => 'optional',
        'repeat_week' => 'optional',
        'repeat_month' => 'optional',
        'repeat_year' => 'optional',
        'date_start' => 'optional',
        'date_end' => 'optional',
        'time_start' => 'optional',
        'time_end' => 'optional',
        'until' => 'optional|date[Y-m-d h:i:s]'

    ];

    // ------------------------------------------------------------------------
    /**
     * Repeaters::$createValidationCustomErrors
     *
     * @var array
     */
    public $createValidationCustomErrors = [
        'repeatable_id' => [
            'required' => 'Repeater id cannot be empty!',
            'integer' => 'Repeater id data must be an integer'
        ],
        'repeatable_model' => [
            'required' => 'Repeater model cannot be empty!'
        ],
        'until' => [
            'date' => 'Repeater date format must be Y-m-d H:i:s'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Repeaters::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
    ];

    // ------------------------------------------------------------------------

    /**
     * Repeaters::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Repeater id cannot be empty!',
            'integer' => 'Repeater id data must be an integer'
        ],
        'repeatable_id' => [
            'required' => 'Repeater id cannot be empty!',
            'integer' => 'Repeater id data must be an integer'
        ],
        'repeatable_model' => [
            'required' => 'Repeater model cannot be empty!'
        ],
        'until' => [
            'date' => 'Repeater date format must be Y-m-d H:i:s'
        ],
    ];

}
