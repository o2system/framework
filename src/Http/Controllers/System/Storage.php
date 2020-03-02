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

namespace O2System\Framework\Http\Controllers\System;

// ------------------------------------------------------------------------

use O2System\Framework\Http\Controllers\Restful;
// ------------------------------------------------------------------------
/**
 * Class Storage
 * @package O2System\Framework\Http\Controllers\System
 */
class Storage extends Restful
{
    /**
     * Storage::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Storage';

    // ------------------------------------------------------------------------
    /**
     * Storage::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [
        'label' => 'required',
        'filename' => 'required',
        'filepath' => 'required',
        'mime' => 'required',
        'ownership_id' => 'required|integer',
        'ownership_model' => 'optional'
    ];

    // ------------------------------------------------------------------------
    /**
     * Storage::$createValidationCustomErrors
     *
     * @var array
     */
    public $createValidationCustomErrors = [
        'label' => [
            'required' => 'Label storage cannot be empty!'
        ],
        'filename' => [
            'required' => 'Filename storage cannot be empty!'
        ],
        'filepath' => [
            'required' => 'Filepath storage cannot be empty!'
        ],
        'mime' => [
            'required' => 'Mime storage cannot be empty!'
        ],
        'ownership_id' => [
            'required' => 'Ownership id cannot be empty!',
            'integer' => 'Ownership id data must be an integer'
        ],
        'ownership_model' => [
            'required' => 'Ownership model cannot be empty!'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Storage::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'label' => 'required',
        'filename' => 'required',
        'filepath' => 'required',
        'mime' => 'required',
        'ownership_id' => 'required|integer',
        'ownership_model' => 'optional'
    ];

    // ------------------------------------------------------------------------

    /**
     * Storage::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Log id cannot be empty!',
            'integer' => 'Log id data must be an integer'
        ],
        'label' => [
            'required' => 'Label storage cannot be empty!'
        ],
        'filename' => [
            'required' => 'Filename storage cannot be empty!'
        ],
        'filepath' => [
            'required' => 'Filepath storage cannot be empty!'
        ],
        'mime' => [
            'required' => 'Mime storage cannot be empty!'
        ],
        'ownership_id' => [
            'required' => 'Ownership id cannot be empty!',
            'integer' => 'Ownership id data must be an integer'
        ],
        'ownership_model' => [
            'required' => 'Ownership model cannot be empty!'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Storage::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Storage::$getValidationCustomErrors
     *
     * @var array
     */
    public $getValidationCustomErrors = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Storage::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
        'label',
        'filename',
        'filepath',
        'mime'
    ];

}