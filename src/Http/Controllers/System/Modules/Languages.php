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
 * Class Languages
 * @package O2System\Framework\Http\Controllers\System\Modules
 */
class Languages extends Restful
{
    /**
     * Languages::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Modules\Languages';

    // ------------------------------------------------------------------------

    /**
     * Languages::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
        'id_sys_module' => 'optional|integer'
    ];

    // ------------------------------------------------------------------------

    /**
     * Languages::$getValidationCustomErrors
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
     * Languages::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
        'key',
        'translation',
    ];
}
