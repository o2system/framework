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
 * Class Relationships
 * @package O2System\Framework\Models\Sql\System
 */
class Relationships extends Model
{
    /**
     * Relationships::$table
     *
     * @var string
     */
    public $table = 'sys_relationships';

    // ------------------------------------------------------------------------

    /**
     * Relationships::$insertValidationRules
     *
     * @var string[]
     */
    public $insertValidationRules = [
        'ownership_id' => 'required',
        'ownership_model' => 'required',
        'relation_id' => 'required|integer',
        'relation_model' => 'required',
        'relation_role' => 'optional'
    ];
    // ------------------------------------------------------------------------
    /**
     * Relationships::$insertValidationCustomErrors
     *
     * @var \string[][]
     */
    public $insertValidationCustomErrors = [
        'ownership_id' => [
            'required' => 'Relationship id cannot be empty!',
        ],
        'ownership_model' => [
            'required' => 'Relationship model cannot be empty!',
        ],
        'relation_id' => [
            'required' => 'Relation id cannot be empty!',
            'integer' => 'Calendars id data must be an integer'
        ],
        'relation_model' => [
            'required' => 'Relation model cannot be empty!'
        ],
    ];
    // ------------------------------------------------------------------------
    /**
     * Relationships::$updateValidationRules
     *
     * @var string[]
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'ownership_id' => 'required',
        'ownership_model' => 'required',
        'relation_id' => 'required|integer',
        'relation_model' => 'required',
        'relation_role' => 'optional'
    ];
    // ------------------------------------------------------------------------
    /**
     * Relationships::$updateValidationCustomErrors
     *
     * @var \string[][]
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Relationship id cannot be empty!',
            'integer' => 'Relationship id data must be an integer'
        ],
        'ownership_id' => [
            'required' => 'Relationship id cannot be empty!',
        ],
        'ownership_model' => [
            'required' => 'Relationship model cannot be empty!',
        ],
        'relation_id' => [
            'required' => 'Relation id cannot be empty!',
            'integer' => 'Calendars id data must be an integer'
        ],
        'relation_model' => [
            'required' => 'Relation model cannot be empty!'
        ],
    ];

    // ------------------------------------------------------------------------
    /**
     * Relationships::ownership
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function ownership()
    {
        return $this->morphTo();
    }

    // ------------------------------------------------------------------------

    /**
     * Relationships::relation
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function relation()
    {
        return $this->morphTo();
    }
}
