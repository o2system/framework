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
 * Class Logs
 * @package O2System\Framework\Http\Controllers\System
 */
class Logs extends Restful
{
    /**
     * Logs::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Logs';

    // ------------------------------------------------------------------------
    /**
     * Logs::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [
        'timestamp' => 'optional|date[Y-m-d h:i:s]',
        'status' => 'optional',
        'message' => 'optional',
        'log_id' => 'required|integer',
        'log_model' => 'required'
    ];

    // ------------------------------------------------------------------------
    /**
     * Logs::$createValidationCustomErrors
     *
     * @var array
     */
    public $createValidationCustomErrors = [
        'timestamp' => [
            'date' => 'Log date format must be Y-m-d H:i:s'
        ],
        'log_id' => [
            'required' => 'Log id cannot be empty!',
            'integer' => 'Log id data must be an integer'
        ],
        'log_model' => [
            'required' => 'Log model id cannot be empty!'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Logs::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'timestamp' => 'optional|date[Y-m-d h:i:s]',
        'status' => 'optional',
        'message' => 'optional',
        'log_id' => 'required|integer',
        'log_model' => 'required'
    ];

    // ------------------------------------------------------------------------

    /**
     * Logs::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Log id cannot be empty!',
            'integer' => 'Log id data must be an integer'
        ],
        'timestamp' => [
            'date' => 'Log date format must be Y-m-d H:i:s'
        ],
        'log_id' => [
            'required' => 'Log id cannot be empty!',
            'integer' => 'Log id data must be an integer'
        ],
        'log_model' => [
            'required' => 'Log model id cannot be empty!'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Logs::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Logs::$getValidationCustomErrors
     *
     * @var array
     */
    public $getValidationCustomErrors = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Logs::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
    ];

}