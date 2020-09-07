<?php


namespace O2System\Framework\Models\Sql\System\Users;


use O2System\Framework\Http\Controllers\System\Modules\Endpoints;
use O2System\Framework\Http\Controllers\System\Users;
use O2System\Framework\Models\Sql\Model;

/**
 * Class Authorities
 * @package O2System\Framework\Models\Sql\System\Users
 */
class Authorities extends Model
{
    /**
     * @var string
     */
    public $table = 'sys_users_authorities';

    // ------------------------------------------------------------------------
    /**
     * Authorities::$insertValidationRules
     *
     * @var array
     */
    public $insertValidationRules = [
        'id_sys_user' => 'required|integer',
        'id_sys_module_endpoint' => 'required',
        'permission' => 'required',
    ];

    // ------------------------------------------------------------------------
    /**
     * Authorities::$insertValidationCustomErrors
     *
     * @var array
     */
    public $insertValidationCustomErrors = [
        'id_sys_user' => [
            'required' => 'Authority id sys user cannot be empty!',
            'integer' => 'Authority id sys user data must be an integer'
        ],
        'id_sys_module_endpoint' => [
            'required' => 'Authority module endpoint cannot be empty!',
        ],
        'permission' => [
            'required' => 'Authority permission cannot be empty!'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Authorities::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'id_sys_user' => 'required|integer',
        'id_sys_module_endpoint' => 'required',
        'permission' => 'required',
    ];

    // ------------------------------------------------------------------------

    /**
     * Authorities::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Authority id cannot be empty!',
            'integer' => 'Authority id data must be an integer'
        ],
        'id_sys_user' => [
            'required' => 'Authority id sys user cannot be empty!',
            'integer' => 'Authority id sys user data must be an integer'
        ],
        'id_sys_module_endpoint' => [
            'required' => 'Authority module endpoint cannot be empty!',
        ],
        'permission' => [
            'required' => 'Authority permission cannot be empty!'
        ],
    ];

    // ------------------------------------------------------------------------
    /**
     * Authorities::user
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function user()
    {
        return $this->belongsTo(Users::class, 'id_sys_user');
    }

    // ------------------------------------------------------------------------
    /**
     * Authorities::moduleEndpoint
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function moduleEndpoint()
    {
        return $this->belongsTo(Endpoints::class, 'id_sys_module_endpoint');
    }

}
