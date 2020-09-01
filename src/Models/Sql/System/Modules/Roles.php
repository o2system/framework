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
 * Class Roles
 * @package O2System\Framework\Models\Sql\System\Modules
 */
class Roles extends Model
{
    /**
     * Roles::$table
     *
     * @var string
     */
    public $table = 'sys_modules_roles';

    // ------------------------------------------------------------------------
    /**
     * Roles::$insertValidationRules
     *
     * @var array
     */
    public $insertValidationRules = [
        'id_sys_module' => 'required|integer',
        'label' => 'required',
        'description' => 'optional',
        'code' => 'required',
    ];

    // ------------------------------------------------------------------------
    /**
     * Roles::$insertValidationCustomErrors
     *
     * @var array
     */
    public $insertValidationCustomErrors = [
        'id_sys_module' => [
            'required' => 'System Module cannot be empty!',
            'integer' => 'System Module data must be an integer'
        ],
        'label' => [
            'required' => 'System Module Role label cannot be empty!'
        ],
        'code' => [
            'required' => 'System Module Role code cannot be empty!'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Roles::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'id_sys_module' => 'required|integer',
        'label' => 'required',
        'description' => 'optional',
        'code' => 'required',
    ];

    // ------------------------------------------------------------------------

    /**
     * Roles::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Role id cannot be empty!',
            'integer' => 'Role id data must be an integer'
        ],
        'id_sys_module' => [
            'required' => 'System Module cannot be empty!',
            'integer' => 'System Module data must be an integer'
        ],
        'label' => [
            'required' => 'System Module Role label cannot be empty!'
        ],
        'code' => [
            'required' => 'System Module Role code cannot be empty!'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Roles::module
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function module()
    {
        return $this->belongsTo(Modules::class);
    }

    // ------------------------------------------------------------------------

    /**
     * Roles::authorities
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function authorities()
    {
        models(Roles\Authorities::class)->appendColumns = [
            'role',
            'endpoint'
        ];

        return $this->hasMany(Roles\Authorities::class);
    }
}
