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
 * Class Tags
 * @package O2System\Framework\Http\Controllers\System
 */
class Tags extends Restful
{
    /**
     * Tags::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Tags';

    // ------------------------------------------------------------------------
    /**
     * Tags::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [
        'ownership_id' => 'required|integer',
        'ownership_model' => 'required',
        'tag_id' => 'required|integer',
        'tag_model' => 'required',
        'tag_role' => 'optional'
    ];

    // ------------------------------------------------------------------------
    /**
     * Tags::$createValidationCustomErrors
     *
     * @var array
     */
    public $createValidationCustomErrors = [
        'ownership_id' => [
            'required' => 'Tag id cannot be empty!',
            'integer' => 'Tag id data must be an integer'
        ],
        'ownership_model' => [
            'required' => 'Ownership model id cannot be empty!'
        ],
        'tag_id' => [
            'required' => 'Tag id cannot be empty!',
            'integer' => 'Tag id data must be an integer'
        ],
        'tag_model' => [
            'required' => 'Tag model id cannot be empty!'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Tags::$updateValidationRules
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
     * Tags::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Tag id cannot be empty!',
            'integer' => 'Tag id data must be an integer'
        ],
        'ownership_id' => [
            'required' => 'Tag id cannot be empty!',
            'integer' => 'Tag id data must be an integer'
        ],
        'ownership_model' => [
            'required' => 'Ownership model id cannot be empty!'
        ],
        'tag_id' => [
            'required' => 'Tag id cannot be empty!',
            'integer' => 'Tag id data must be an integer'
        ],
        'tag_model' => [
            'required' => 'Tag model id cannot be empty!'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Tags::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Tags::$getValidationCustomErrors
     *
     * @var array
     */
    public $getValidationCustomErrors = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Tags::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
    ];


}