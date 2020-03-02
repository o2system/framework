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
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Users\Comments';

    // ------------------------------------------------------------------------
    /**
     * Comments::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [
        'id_sys_user' => 'required|integer',
        'message' => 'required',
        'commentable_id' => 'required|integer',
        'commentatble_model' => 'required',

    ];

    // ------------------------------------------------------------------------
    /**
     * Comments::$createValidationCustomErrors
     *
     * @var array
     */
    public $createValidationCustomErrors = [
        'id_sys_user' => [
            'required' => 'System User cannot be empty!',
            'integer' => 'System User data must be an integer'
        ],
        'message' => [
            'required' => 'System User comment message cannot be empty!',
        ],
        'commentable_id' => [
            'required' => 'System User commentable id cannot be empty!',
            'integer' => 'System User commentable id data must be an integer'
        ],
        'commentatble_model' => [
            'required' => 'System User commentable model cannot be empty!',
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Comments::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'id_sys_user' => 'required|integer',
        'message' => 'required',
        'commentable_id' => 'required|integer',
        'commentatble_model' => 'required',
    ];

    // ------------------------------------------------------------------------

    /**
     * Comments::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Comment id cannot be empty!',
            'integer' => 'Comment id data must be an integer'
        ],
        'id_sys_user' => [
            'required' => 'System User cannot be empty!',
            'integer' => 'System User data must be an integer'
        ],
        'message' => [
            'required' => 'System User comment message cannot be empty!',
        ],
        'commentable_id' => [
            'required' => 'System User commentable id cannot be empty!',
            'integer' => 'System User commentable id data must be an integer'
        ],
        'commentatble_model' => [
            'required' => 'System User commentable model cannot be empty!',
        ],
    ];

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