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

use App\Api\Controllers\System\Modules\Users;
use O2System\Framework\Models\Sql\Model;

/**
 * Class Sessions
 * @package O2System\Framework\Models\Sql\System\Users
 */
class Sessions extends Model
{
    /**
     * Sessions::$table
     *
     * @var string
     */
    public $table = 'sys_users_sessions';

    // ------------------------------------------------------------------------

    /**
     * Sessions::user
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function user()
    {
        return $this->belongsTo(Users::class);
    }
}