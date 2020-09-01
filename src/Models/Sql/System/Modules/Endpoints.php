<?php


namespace O2System\Framework\Models\Sql\System\Modules;

// ------------------------------------------------------------------------
use O2System\Framework\Models\Sql\Model;

/**
 * Class Endpoints
 * @package O2System\Framework\Models\Sql\System\Modules
 */
class Endpoints extends Model
{
    /**
     * Endpoints::$table
     *
     * @var string
     */
    public $table = 'sys_modules_endpoints';

    // ------------------------------------------------------------------------
    /**
     * Endpoints::$insertValidationRules
     *
     * @var array
     */
    public $insertValidationRules = [
        'id_sys_module' => 'required|integer',
        'name' => 'required',
        'uri' => 'required',
        'slug' => 'required',
        'class' => 'required',
    ];

    // ------------------------------------------------------------------------
    /**
     * Endpoints::$insertValidationCustomErrors
     *
     * @var array
     */
    public $insertValidationCustomErrors = [
        'id_sys_module' => [
            'required' => 'System Module cannot be empty!',
            'integer' => 'System Module data must be an integer'
        ],
        'name' => [
            'required' => 'System module endpoint name cannot be empty!'
        ],
        'uri' => [
            'required' => 'System module endpoint uri cannot be empty!'
        ],
        'slug' => [
            'required' => 'System module endpoint slug cannot be empty!'
        ],
        'class' => [
            'required' => 'System module endpoint class cannot be empty!'
        ],
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
        'name' => [
            'required' => 'System module endpoint name cannot be empty!'
        ],
        'uri' => [
            'required' => 'System module endpoint uri cannot be empty!'
        ],
        'slug' => [
            'required' => 'System module endpoint slug cannot be empty!'
        ],
        'class' => [
            'required' => 'System module endpoint class cannot be empty!'
        ],
    ];
}
