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

use O2System\Framework\Models\Sql\System\Users;
use O2System\Framework\Models\Sql\Model;

/**
 * Class Actions
 * @package O2System\Framework\Models\Sql\System\Users
 */
class Actions extends Model
{
    /**
     * Actions::$table
     *
     * @var string
     */
    public $table = 'sys_users_actions';
    // ------------------------------------------------------------------------
    /**
     * Actions::$insertValidationRules
     *
     * @var array
     */
    public $insertValidationRules = [
        'id_sys_user' => 'required|integer',
        'action' => 'optional',
        'role' => 'required',
        'step' => 'optional',
        'reference_id' => 'required|integer',
        'reference_model' => 'required'
    ];

    // ------------------------------------------------------------------------

    /**
     * Actions::$insertValidationCustomErrors
     *
     * @var array
     */
    public $insertValidationCustomErrors = [
        'id_sys_user' => [
            'required' => 'System User cannot be empty!',
            'integer' => 'System User data must be an integer'
        ],
        'role' => ['required' => 'User Action role cannot be empty!'],
        'reference_id' => [
            'required' => 'User Action reference cannot be empty!',
            'integer' => 'System User data must be an integer'
        ],
        'reference_model' => ['required' => 'User Action reference model cannot be empty!'],
    ];

    // ------------------------------------------------------------------------

    /**
     * Actions::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'id_sys_user' => 'required|integer',
        'action' => 'optional',
        'role' => 'required',
        'step' => 'optional',
        'reference_id' => 'required|integer',
        'reference_model' => 'required'
    ];

    // ------------------------------------------------------------------------

    /**
     * Actions::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Action id cannot be empty!',
            'integer' => 'Action id data must be an integer'
        ],
        'id_sys_user' => [
            'required' => 'System User cannot be empty!',
            'integer' => 'System User data must be an integer'
        ],
        'role' => ['required' => 'User Action role cannot be empty!'],
        'reference_id' => [
            'required' => 'User Action reference cannot be empty!',
            'integer' => 'System User data must be an integer'
        ],
        'reference_model' => ['required' => 'User Action reference model cannot be empty!'],
    ];

    // ------------------------------------------------------------------------


    // ------------------------------------------------------------------------

    /**
     * Actions::user
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function user()
    {
        return $this->belongsTo(Users::class, 'id_sys_user');
    }

    // ------------------------------------------------------------------------

    /**
     * Actions::reference
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function reference()
    {
        return $this->morphTo();
    }
}
