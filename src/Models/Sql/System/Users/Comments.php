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

/**
 * Class Comments
 * @package O2System\Framework\Models\Sql\System\Users
 */
class Comments extends Model
{
    /**
     * Comments::$table
     *
     * @var string
     */
    public $table = 'sys_users_comments';

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