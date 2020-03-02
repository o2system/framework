<?php


namespace O2System\Framework\Http\Controllers\System;


use O2System\Framework\Http\Controllers\Restful;

class Consumers extends Restful
{
    /**
     * Consumers::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Consumers';

    // ------------------------------------------------------------------------
    /**
     * Consumers::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [
        'namespace' => 'required',
        'key' => 'required',
        'secret' => 'required',
        'callback' => 'optional'
    ];

    // ------------------------------------------------------------------------
    /**
     * Consumers::$createValidationCustomErrors
     *
     * @var array
     */
    public $createValidationCustomErrors = [
        'namespace' => [
            'required' => 'Consumer namespace cannot be empty!'
        ],
        'key' => [
            'required' => 'Consumer key cannot be empty!'
        ],
        'secret' => [
            'required' => 'Consumer secret cannot be empty!'
        ]
    ];

    // ------------------------------------------------------------------------

    /**
     * Consumers::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'namespace' => 'required',
        'key' => 'required',
        'secret' => 'required',
        'callback' => 'optional'
    ];

    // ------------------------------------------------------------------------

    /**
     * Consumers::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Consumer id cannot be empty!',
            'integer' => 'Consumer id data must be an integer'
        ],
        'namespace' => [
            'required' => 'Consumer namespace cannot be empty!'
        ],
        'key' => [
            'required' => 'Consumer key cannot be empty!'
        ],
        'secret' => [
            'required' => 'Consumer secret cannot be empty!'
        ]

    ];

    // ------------------------------------------------------------------------

    /**
     * Consumers::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Consumers::$getValidationCustomErrors
     *
     * @var array
     */
    public $getValidationCustomErrors = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Consumers::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
    ];
}