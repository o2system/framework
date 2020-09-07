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

namespace O2System\Framework\Models\Sql\System\Users;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\System\Users;
use O2System\Framework\Models\Sql\Traits\MetadataTrait;

/**
 * Class Comments
 * @package O2System\Framework\Models\Sql\System\Users
 */
class Comments extends Model
{
    use MetadataTrait;
    /**
     * Comments::$table
     *
     * @var string
     */
    public $table = 'sys_users_comments';

    // ------------------------------------------------------------------------
    /**
     * Comments::$insertValidationRules
     *
     * @var array
     */
    public $insertValidationRules = [
        'id_sys_user' => 'required|integer',
        'message' => 'required',
        'commentable_id' => 'required|integer',
        'commentatble_model' => 'required',

    ];

    // ------------------------------------------------------------------------
    /**
     * Comments::$insertValidationCustomErrors
     *
     * @var array
     */
    public $insertValidationCustomErrors = [
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

    /**
     * Comments::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        'user'
    ];

    // ------------------------------------------------------------------------

    /**
     * Comments::user
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function user()
    {
        return $this->belongsTo(Users::class);
    }

    // ------------------------------------------------------------------------

    /**
     * Comments::reference
     *
     * @return mixed
     */
    public function reference()
    {
        return $this->morphTo();
    }
}
