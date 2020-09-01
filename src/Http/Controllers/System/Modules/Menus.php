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
