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
     * Logs::__construct
     *
     * @throws \ReflectionException
     */
    public function __construct()
    {
        parent::__construct();

        $this->qb->orderBy($this->primaryKey, 'DESC');
    }

    // ------------------------------------------------------------------------
    /**
     * Logs::$insertValidationRules
     *
     * @var array
     */
    public $insertValidationRules = [
        'ownership_id' => 'required',
        'ownership_model' => 'required',
        'timestamp' => 'required|date[Y-m-d h:i:s]',
        'status' => 'required',
        'message' => 'optional'
    ];

    // ------------------------------------------------------------------------
    /**
     * Logs::$insertValidationCustomErrors
     *
     * @var array
     */
    public $insertValidationCustomErrors = [
        'ownership_id' => [
            'required' => 'Log ownership id cannot be empty!',
        ],
        'ownership_model' => [
            'required' => 'Log ownership model cannot be empty!',
        ],
        'timestamp' => [
            'required' => 'Log timestamp cannot be empty!',
            'date' => 'Log timestamp format must be Y-m-d H:i:s'
        ],
        'status' => [
            'required' => 'Log status cannot be empty!'
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
        'ownership_id' => 'required',
        'ownership_model' => 'required',
        'timestamp' => 'required|date[Y-m-d h:i:s]',
        'status' => 'required',
        'message' => 'optional'
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
        'ownership_id' => [
            'required' => 'Log ownership id cannot be empty!',
        ],
        'ownership_model' => [
            'required' => 'Log ownership model cannot be empty!',
        ],
        'timestamp' => [
            'required' => 'Log timestamp cannot be empty!',
            'date' => 'Log timestamp format must be Y-m-d H:i:s'
        ],
        'status' => [
            'required' => 'Log status cannot be empty!'
        ],
    ];

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
