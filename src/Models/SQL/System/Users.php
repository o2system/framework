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

namespace O2System\Framework\SQL\Models;

// ------------------------------------------------------------------------

use O2System\Orm\Abstracts\AbstractModel;
use O2System\Framework\Libraries\Acl\Datastructures\Account;

/**
 * Class Users
 *
 * @package O2System\Framework\SQL\Models\Users
 */
class Users extends AbstractModel
{
    public $table = 'sys_users';

    public function insert( array $data )
    {
        return $this->db->table( $this->table )->insert( $data );
    }

    public function update( Account $account )
    {
        return $this->db->table( $this->table )
                        ->where( 'username', $account->username )
                        ->update( $account->getArrayCopy() );
    }
}