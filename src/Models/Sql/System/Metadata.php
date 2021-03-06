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
 * Class Metadata
 * @package O2System\Framework\Models\Sql\System
 */
class Metadata extends Model
{
    /**
     * Metadata::$table
     *
     * @var string
     */
    public $table = 'sys_metadata';
    // ------------------------------------------------------------------------
    /**
     * Metadata::$insertValidationRules
     *
     * @var array
     */
    public $insertValidationRules = [
        'ownership_id' => 'required|integer',
        'ownership_model' => 'required',
        'name' => 'required',
        'content' => 'optional'
    ];

    // ------------------------------------------------------------------------
    /**
     * Metadata::$insertValidationCustomErrors
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
        'name' => [
            'required' => 'Metadata name cannot be empty!'
        ]
    ];

    // ------------------------------------------------------------------------

    /**
     * Metadata::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
    ];

    // ------------------------------------------------------------------------

    /**
     * Metadata::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Metadata id cannot be empty!',
        ],
        'ownership_id' => [
            'required' => 'Ownership id cannot be empty!',
            'integer' => 'Ownership id data must be an integer'
        ],
        'ownership_model' => [
            'required' => 'Ownership model cannot be empty!'
        ],
        'name' => [
            'required' => 'Metadata name cannot be empty!'
        ]
    ];

    // ------------------------------------------------------------------------

    /**
     * Metadata::ownership
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function ownership()
    {
        return $this->morphTo();
    }
}
