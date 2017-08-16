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

namespace O2System\Framework\SQL\Models\Users;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Acl\Datastructures\Account;
use O2System\Framework\SQL\Models\Users;

/**
 * Class Role
 *
 * @package O2System\Framework\SQL\Models\Users
 */
class Role extends Users
{
    public $table = 'sys_users_roles';

    public function insert( array $data )
    {
        return $this->db->table( $this->table )->insert( $data );
    }
}