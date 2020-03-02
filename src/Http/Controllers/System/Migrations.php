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
use App\Api\Http\Controller;
// ------------------------------------------------------------------------
/**
 * Class Migrations
 * @package O2System\Framework\Http\Controllers\System
 */
class Migrations extends Restful
{
    /**
     * Migrations::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Migrations';

    // ------------------------------------------------------------------------
    /**
     * Migrations::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [
    ];

    // ------------------------------------------------------------------------
    /**
     * Migrations::$createValidationCustomErrors
     *
     * @var array
     */
    public $createValidationCustomErrors = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Migrations::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
    ];

    // ------------------------------------------------------------------------

    /**
     * Migrations::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Migration id cannot be empty!',
            'integer' => 'Migration id data must be an integer'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Migrations::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Migrations::$getValidationCustomErrors
     *
     * @var array
     */
    public $getValidationCustomErrors = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Migrations::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
    ];

}