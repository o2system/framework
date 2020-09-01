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

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;

/**
 * Class Logs
 * @package O2System\Framework\Models\Sql\System
 */
class Logs extends Model
{
    /**
     * Logs::$table
     *
     * @var string
     */
    public $table = 'sys_logs';

    // ------------------------------------------------------------------------
    /**
     * Logs::$insertValidationRules
     *
     * @var array
     */
    public $insertValidationRules = [
        'timestamp' => 'optional|date[Y-m-d h:i:s]',
        'status' => 'optional',
        'message' => 'optional',
        'log_id' => 'required|integer',
        'log_model' => 'required'
    ];

    // ------------------------------------------------------------------------
    /**
     * Logs::$insertValidationCustomErrors
     *
     * @var array
     */
    public $insertValidationCustomErrors = [
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

    /**
     * Logs::__construct
     * @throws \ReflectionException
     */
    public function __construct()
    {
        parent::__construct();

        $this->qb->orderBy($this->primaryKey, 'DESC');
    }

    // ------------------------------------------------------------------------

    /**
     * Logs::ownership
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function ownership()
    {
        return $this->morphTo();
    }
}
