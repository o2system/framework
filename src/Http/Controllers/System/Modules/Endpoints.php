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

namespace O2System\Framework\Http\Controllers\System\Modules;


use O2System\Framework\Http\Controllers\Restful;

/**
 * Class Endpoints
 * @package O2System\Framework\Http\Controllers\System\Modules
 */
class Endpoints extends Restful
{
    /**
     * Endpoints::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Modules\Endpoints';

    // ------------------------------------------------------------------------
    /**
     * Endpoints::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [
        'id_sys_module' => 'required|integer',
        'name' => 'required',
        'uri' => 'required',
        'slug' => 'required',
        'class' => 'required',
    ];

    // ------------------------------------------------------------------------
    /**
     * Endpoints::$createValidationCustomErrors
     *
     * @var array
     */
    public $createValidationCustomErrors = [
        'id_sys_module' => [
            'required' => 'System Module cannot be empty!',
            'integer' => 'System Module data must be an integer'
        ],
        'name' => ['required' => 'System module endpoint name cannot be empty!'],
        'uri' => ['required' => 'System module endpoint uri cannot be empty!'],
        'slug' => ['required' => 'System module endpoint slug cannot be empty!'],
        'class' => ['required' => 'System module endpoint class cannot be empty!'],
    ];

    // ------------------------------------------------------------------------

    /**
     * Endpoints::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'id_sys_module' => 'required|integer',
        'name' => 'required',
        'uri' => 'required',
        'slug' => 'required',
        'class' => 'required',
    ];

    // ------------------------------------------------------------------------

    /**
     * Endpoints::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Endpoint id cannot be empty!',
            'integer' => 'Endpoint id data must be an integer'
        ],
        'id_sys_module' => [
            'required' => 'System Module cannot be empty!',
            'integer' => 'System Module data must be an integer'
        ],
        'name' => ['required' => 'System module endpoint name cannot be empty!'],
        'uri' => ['required' => 'System module endpoint uri cannot be empty!'],
        'slug' => ['required' => 'System module endpoint slug cannot be empty!'],
        'class' => ['required' => 'System module endpoint class cannot be empty!'],
    ];

    // ------------------------------------------------------------------------

    /**
     * Endpoints::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
        'id_sys_module' => 'optional|integer',
    ];

    // ------------------------------------------------------------------------

    /**
     * Endpoints::$getValidationCustomErrors
     *
     * @var array
     */
    public $getValidationCustomErrors = [
        'id_sys_module' => [
            'integer' => 'System Module data must be an integer'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Endpoints::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
        'name',
    ];
}