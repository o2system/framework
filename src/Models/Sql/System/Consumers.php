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

namespace O2System\Framework\Models\Sql\System;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\System\Consumers\Authorities;
use O2System\Framework\Models\Sql\System\Consumers\Sessions;

/**
 * Class Consumers
 * @package O2System\Framework\Models\Sql\System
 */
class Consumers extends Model
{
    /**
     * Consumers::$table
     *
     * @var string
     */
    public $table = 'sys_consumers';

    // ------------------------------------------------------------------------

    /**
     * Consumers::authorities
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function authorities()
    {
        return $this->hasMany(Authorities::class);
    }

    // ------------------------------------------------------------------------

    /**
     * Consumers::sessions
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function sessions()
    {
        return $this->hasMany(Sessions::class);
    }
}