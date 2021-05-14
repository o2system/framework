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
namespace O2System\Framework\Http\Controllers\System\Users;

// ------------------------------------------------------------------------
use O2System\Framework\Http\Controllers\Restful;
// ------------------------------------------------------------------------
/**
 * Class Comments
 * @package O2System\Framework\Http\Controllers\System\Users
 */
class Comments extends Restful
{
    /**
     * Comments::$model
     *
     * @var string|\O2System\Framework\Models\Sql\System\Users\Comments
     */
    public $model = '\O2System\Framework\Models\Sql\System\Users\Comments';


    // ------------------------------------------------------------------------

    /**
     * Comments::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
        'id_sys_user' => 'optional|integer',
    ];

    // ------------------------------------------------------------------------

    /**
     * Comments::$getValidationCustomErrors
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
     * Comments::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
        'message'
    ];

}
