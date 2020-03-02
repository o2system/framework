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
use O2System\Framework\Models\Sql\Traits\MetadataTrait;
use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class Notifications
 * @package O2System\Framework\Models\Sql\System\Users
 */
class Notifications extends Model
{
    /**
     * Notifications::$table
     *
     * @var string
     */
    public $table = 'sys_users_notifications';

    // ------------------------------------------------------------------------

    /**
     * Notifications::user
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function user()
    {
        return $this->belongsTo(Users::class);
    }

    // ------------------------------------------------------------------------

    /**
     * Notifications::sender
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function sender()
    {
        return $this->belongsTo(Users::class, 'id_sys_user_sender');
    }

    // ------------------------------------------------------------------------

    /**
     * Notifications::notification
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function notification()
    {
        return $this->morphTo();
    }
}