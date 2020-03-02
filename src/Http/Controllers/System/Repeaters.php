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
namespace O2System\Framework\Http\Controllers\System;

// ------------------------------------------------------------------------
use O2System\Framework\Http\Controllers\Restful;
// ------------------------------------------------------------------------
/**
 * Class Repeaters
 * @package O2System\Framework\Http\Controllers\System
 */
class Repeaters extends Restful
{
    /**
     * Repeaters::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Repeaters';

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

    // ------------------------------------------------------------------------

    /**
     * Repeaters::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Repeaters::$getValidationCustomErrors
     *
     * @var array
     */
    public $getValidationCustomErrors = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Repeaters::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
        'repeat_minute',
        'repeat_hour',
        'repeat_day',
        'repeat_date',
        'repeat_week',
        'repeat_month',
        'repeat_year',
        'date_start',
        'date_end',
        'time_start',
        'time_end',
        'until',
    ];

}