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
 * Class Settings
 * @package O2System\Framework\Http\Controllers\System
 */
class Settings extends Restful
{
    /**
     * Settings::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Settings';

    // ------------------------------------------------------------------------
    /**
     * Settings::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [
        'ownership_id' => 'required|integer',
        'ownership_model' => 'required',
        'key' => 'required',
        'value' => 'optional'
    ];

    // ------------------------------------------------------------------------
    /**
     * Settings::$createValidationCustomErrors
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
        'key' => [
            'required' => 'Setting name cannot be empty!'
        ]
    ];

    // ------------------------------------------------------------------------

    /**
     * Settings::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
    ];

    // ------------------------------------------------------------------------

    /**
     * Settings::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Settings id cannot be empty!',
            'integer' => 'Settings id data must be an integer'
        ],
        'ownership_id' => [
            'required' => 'Ownership id cannot be empty!',
            'integer' => 'Ownership id data must be an integer'
        ],
        'ownership_model' => [
            'required' => 'Ownership model cannot be empty!'
        ],
        'key' => [
            'required' => 'Setting name cannot be empty!'
        ]
    ];

    // ------------------------------------------------------------------------

    /**
     * Settings::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Settings::$getValidationCustomErrors
     *
     * @var array
     */
    public $getValidationCustomErrors = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Settings::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
        'key',
        'value'
    ];

}