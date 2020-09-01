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

namespace O2System\Framework\Models\Sql\System;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\HierarchicalTrait;
use O2System\Framework\Models\Sql\Traits\MetadataTrait;
use O2System\Framework\Models\Sql\Traits\SettingsTrait;

/**
 * Class Modules
 * @package O2System\Framework\Models\Sql\System\Models
 */
class Modules extends Model
{
    use MetadataTrait;
    use SettingsTrait;
    use HierarchicalTrait;

    /**
     * Modules::$table
     *
     * @var string
     */
    public $table = 'sys_modules';

    /**
     * Modules::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        'settings'
    ];

    // ------------------------------------------------------------------------

    /**
     * Modules::$createValidationRules
     *
     * @var array
     */
    public $insertValidationRules = [
        'id_parent' => 'required|integer',
        'segments' => 'required',
        'namespace' => 'required',
        'path' => 'required',
        'type'   => 'required',
        'metadata'   => 'required',
    ];

    // ------------------------------------------------------------------------

    /**
     * Modules::$createValidationCustomErrors
     *
     * @var array
     */
    public $insertValidationCustomErrors = [
        'id_parent' => [
            'required' => 'Parent cannot be empty!',
            'integer' => 'Parent data must be an integer'
        ],
        'segments' => ['required' => 'Module segments cannot be empty!'],
        'namespace' => ['required' => 'Module namespace cannot be empty!'],
        'path' => ['required' => 'Module path cannot be empty!'],
        'type' => ['required' => 'Module type cannot be empty!'],
        'metadata' => ['required' => 'Module metadata cannot be empty!'],
    ];

    // ------------------------------------------------------------------------

    /**
     * Modules::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'id_parent' => 'required|integer',
        'segments' => 'required',
        'namespace' => 'required',
        'path' => 'required',
        'type'   => 'required',
        'metadata'   => 'required',
    ];

    // ------------------------------------------------------------------------

    /**
     * Modules::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Module id cannot be empty!',
            'integer' => 'Module id data must be an integer'
        ],
        'id_parent' => [
            'required' => 'Parent cannot be empty!',
            'integer' => 'Parent data must be an integer'
        ],
        'segments' => ['required' => 'Module segments cannot be empty!'],
        'namespace' => ['required' => 'Module namespace cannot be empty!'],
        'path' => ['required' => 'Module path cannot be empty!'],
        'type' => ['required' => 'Module type cannot be empty!'],
        'metadata' => ['required' => 'Module metadata cannot be empty!'],
    ];

    // ------------------------------------------------------------------------

    /**
     * Modules::roles
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function roles()
    {
        return $this->hasMany(Modules\Roles::class);
    }
}
