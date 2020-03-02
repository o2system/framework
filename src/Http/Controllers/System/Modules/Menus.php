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

// ------------------------------------------------------------------------

use O2System\Framework\Http\Controllers\Restful;

/**
 * Class Menus
 * @package O2System\Framework\Http\Controllers\System\Modules
 */
class Menus extends Restful
{
    /**
     * Menus::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Modules\Menus';

    // ------------------------------------------------------------------------
    /**
     * Menus::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [
        'id_sys_module' => 'optional|integer',
        'id_parent' => 'optional|integer',
        'position' => 'optional',
        'label' => 'optional',
        'description' => 'optional',
        'href' => 'optional',
        'attributes' => 'optional',
        'settings' => 'optional',
        'metadata' => 'optional',
    ];

    // ------------------------------------------------------------------------
    /**
     * Menus::$createValidationCustomErrors
     *
     * @var array
     */
    public $createValidationCustomErrors = [
        'id_sys_module' => [
            'integer' => 'System Module data must be an integer'
        ],
        'id_parent' => [
            'integer' => 'Parent data must be an integer'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Menus::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'id_sys_module' => 'optional|integer',
        'id_parent' => 'optional|integer',
        'position' => 'optional',
        'label' => 'optional',
        'description' => 'optional',
        'href' => 'optional',
        'attributes' => 'optional',
        'settings' => 'optional',
        'metadata' => 'optional',
    ];

    // ------------------------------------------------------------------------

    /**
     * Menus::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Menu id cannot be empty!',
            'integer' => 'Menu id data must be an integer'
        ],
        'id_sys_module' => [
            'integer' => 'System Module data must be an integer'
        ],
        'id_parent' => [
            'integer' => 'Parent data must be an integer'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Menus::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
        'id_sys_module' => 'optional|integer',
        'id_parent' => 'optional|integer',
    ];

    // ------------------------------------------------------------------------

    /**
     * Menus::$getValidationCustomErrors
     *
     * @var array
     */
    public $getValidationCustomErrors = [
        'id_sys_module' => [
            'integer' => 'System Module data must be an integer'
        ],
        'id_parent' => [
            'integer' => 'Parent data must be an integer'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Menus::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
        'label',
        'description',
    ];
}