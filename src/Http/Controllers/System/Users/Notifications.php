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
 * Class Notifications
 * @package O2System\Framework\Http\Controllers\System\Users
 */
class Notifications extends Restful
{
    /**
     * Notifications::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Users\Notifications';



    // ------------------------------------------------------------------------

    /**
     * Notifications::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Notifications::$getValidationCustomErrors
     *
     * @var array
     */
    public $getValidationCustomErrors = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Notifications::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
        'message',
        'timestamp',
        'status',
        'metadata'
    ];

}
