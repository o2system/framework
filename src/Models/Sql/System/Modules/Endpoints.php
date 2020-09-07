<?php


namespace O2System\Framework\Models\Sql\System\Modules;

// ------------------------------------------------------------------------
use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\System\Modules;

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
        'endpoint' => 'required',
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
        'endpoint' => [
            'required' => 'System module endpoint uri cannot be empty!'
        ]
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
        'endpoint' => 'required',
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
        'endpoint' => [
            'required' => 'System module endpoint uri cannot be empty!'
        ]
    ];
    // ------------------------------------------------------------------------

    /**
     * Endpoints::module
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function module()
    {
        return $this->belongsTo(Modules::class);
    }

    // ------------------------------------------------------------------------
    /**
     * Endpoints::authorities
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function authorities()
    {
        return $this->hasMany(Authorities::class);
    }
}
