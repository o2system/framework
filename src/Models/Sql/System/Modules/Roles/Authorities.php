<?php
namespace O2System\Framework\Models\Sql\System\Modules\Roles;

use O2System\Framework\Models\Sql\Model;

/**
 * Class Authorities
 * @package O2System\Framework\Models\Sql\System\Modules\Roles
 */
class Authorities extends Model
{
    /**
     * Authorities::$table
     *
     * @var string
     */
    public $table = 'sys_modules_roles_authorities';

    // ------------------------------------------------------------------------
    /**
     * Authorities::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [
        'id_sys_module_role' => 'required|integer',
        'id_sys_module_endpoint' => 'required|integer',
        'permission' => 'required|listed[DENIED,GRANTED,WRITE]'
    ];

    // ------------------------------------------------------------------------
    /**
     * Authorities::$createValidationCustomErrors
     *
     * @var array
     */
    public $createValidationCustomErrors = [
        'id_sys_module' => [
            'required' => 'System Module cannot be empty!',
            'integer' => 'System Module data must be an integer'
        ],
        'id_sys_module_endpoint' => [
            'required' => 'System module endpoint  cannot be empty!',
            'integer' => 'System module endpoint data must be an integer'
        ],
        'permission' => [
            'required' => 'System module role authority cannot be empty!',
            'listed' => 'System module role authority permission must be listed: DENIED,GRANTED,WRITE'
        ]

    ];

    // ------------------------------------------------------------------------

    /**
     * Authorities::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'id_sys_module_role' => 'required|integer',
        'id_sys_module_endpoint' => 'rquired|integer',
        'permission' => 'required|listed[DENIED,GRANTED,WRITE]'
    ];

    // ------------------------------------------------------------------------

    /**
     * Authorities::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Setting id cannot be empty!',
            'integer' => 'Setting id data must be an integer'
        ],
        'id_sys_module' => [
            'required' => 'System Module cannot be empty!',
            'integer' => 'System Module data must be an integer'
        ],
        'id_sys_module_endpoint' => [
            'required' => 'System module endpoint  cannot be empty!',
            'integer' => 'System module endpoint data must be an integer'
        ],
        'permission' => [
            'required' => 'System module role authority cannot be empty!',
            'listed' => 'System module role authority permission must be listed: DENIED,GRANTED,WRITE'
        ]
    ];

}
