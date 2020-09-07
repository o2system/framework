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

namespace O2System\Framework\Models\Sql\System\Modules;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\System\Modules;

/**
 * Class Authorities
 * @package O2System\Framework\Models\Sql\System\Modules
 */
class Authorities extends Model
{
   /**
     * Authorities::$table
     *
     * @var string
     */
    public $table = 'sys_modules_authorities';
    // ------------------------------------------------------------------------
    /**
     * Authorities::$insertValidationRules
     *
     * @var array
     */
    public $insertValidationRules = [
        'id_sys_module' => 'required|integer',
        'endpoint' => 'required',
        'permission' => 'required',
        'scope' => 'optional',
        'ownership_id' => 'required',
        'ownership_model' => 'required'
    ];

    // ------------------------------------------------------------------------
    /**
     * Authorities::$insertValidationCustomErrors
     *
     * @var array
     */
    public $insertValidationCustomErrors = [
        'id_sys_module' => [
            'required' => 'Authority id sys module cannot be empty!',
            'integer' => 'Authority id sys module data must be an integer'
        ],
        'endpoint' => [
            'required' => 'Authority endpoint cannot be empty!',
        ],
        'permission' => [
            'required' => 'Authority permission cannot be empty!'
        ],
        'ownership_id' => [
            'required' => 'Authority  ownership id sys consumer cannot be empty!',
            'integer' => 'Authority ownership id sys consumer data must be an integer'
        ],
        'ownership_model' => [
            'required' => 'Authority ownership model  cannot be empty!',
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
        'id_sys_module' => 'required|integer',
        'endpoint' => 'required',
        'permission' => 'required',
        'scope' => 'optional',
        'ownership_id' => 'required',
        'ownership_model' => 'required'
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
        'id_sys_module' => [
            'required' => 'Authority id sys module cannot be empty!',
            'integer' => 'Authority id sys module data must be an integer'
        ],
        'endpoint' => [
            'required' => 'Authority endpoint cannot be empty!',
        ],
        'permission' => [
            'required' => 'Authority permission cannot be empty!'
        ],
        'ownership_id' => [
            'required' => 'Authority  ownership id sys consumer cannot be empty!',
            'integer' => 'Authority ownership id sys consumer data must be an integer'
        ],
        'ownership_model' => [
            'required' => 'Authority ownership model  cannot be empty!',
        ]
    ];

    // ------------------------------------------------------------------------

    /**
     * Authorities::module
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function module()
    {
        return $this->belongsTo(Modules::class);
    }

    // ------------------------------------------------------------------------

    /**
     * Authorities::ownership
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function ownership()
    {
        return $this->morphTo();
    }

    /**
     * Authorities::isChecked
     *
     * @param $module
     * @param $endpoint
     * @param $role
     * @param $permission
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function isChecked($module, $endpoint, $role, $permission)
    {
        $result = $this->findWhere([
            'id_sys_module' => $module,
            'endpoint' => $endpoint,
            'ownership_id' => $role,
            'permission' => $permission
        ]);

        if ($result->count())
            return true;

        return false;
    }
}
