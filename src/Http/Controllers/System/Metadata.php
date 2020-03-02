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

/**
 * Class Metadata
 * @package O2System\Framework\Http\Controllers\System
 */
class Metadata extends Restful
{
    /**
     * Metadata::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Metadata';

    // ------------------------------------------------------------------------
    /**
     * Metadata::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [
        'ownership_id' => 'required|integer',
        'ownership_model' => 'required',
        'name' => 'required',
        'content' => 'optional'
    ];

    // ------------------------------------------------------------------------
    /**
     * Metadata::$createValidationCustomErrors
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
            'integer' => 'Metadata id data must be an integer'
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
     * Metadata::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Metadata::$getValidationCustomErrors
     *
     * @var array
     */
    public $getValidationCustomErrors = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Metadata::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
        'name',
        'content'
    ];

}