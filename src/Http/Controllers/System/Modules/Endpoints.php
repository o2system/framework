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


use O2System\Framework\Http\Controllers\Restful;

/**
 * Class Endpoints
 * @package O2System\Framework\Http\Controllers\System\Modules
 */
class Endpoints extends Restful
{
    /**
     * Endpoints::$model
     *
     * @var string|\O2System\Framework\Models\Sql\System\Modules\Endpoints
     */
    public $model = '\O2System\Framework\Models\Sql\System\Modules\Endpoints';

    // ------------------------------------------------------------------------

    /**
     * Endpoints::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
        'id_sys_module' => 'optional|integer',
    ];

    // ------------------------------------------------------------------------

    /**
     * Endpoints::$getValidationCustomErrors
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
     * Endpoints::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
        'name',
    ];
}
