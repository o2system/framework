<?php
/**
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */
// ------------------------------------------------------------------------

namespace O2System\Framework\Models\Users;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Users;

/**
 * Class Profile
 *
 * @package O2System\Framework\Models\Users
 */
class Profile extends Users
{
    public $table = 'sys_users_profiles';

    public function insert( array $data )
    {
        return $this->db->table( $this->table )->insert( $data );
    }
}