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
namespace O2System\Framework\Http\Controllers\System\Users;
// ------------------------------------------------------------------------

use O2System\Framework\Http\Controllers\Restful;
// ------------------------------------------------------------------------
/**
 * Class Modules
 * @package O2System\Framework\Http\Controllers\System\Users
 */
class Modules extends Restful
{
    /**
     * Modules::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Users\Modules';



    // ------------------------------------------------------------------------

    /**
     * Modules::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
        'id_sys_user' => 'optional|integer',
    ];

    // ------------------------------------------------------------------------

    /**
     * Modules::$getValidationCustomErrors
     *
     * @var array
     */
    public $getValidationCustomErrors = [
        'id_sys_user' => [
            'integer' => 'System User data must be an integer'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Modules::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
    ];

}
