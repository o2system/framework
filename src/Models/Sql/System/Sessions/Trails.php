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

namespace O2System\Framework\Models\Sql\System\Sessions;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\System\Sessions;

/**
 * Class Trails
 * @package O2System\Framework\Models\Sql\System
 */
class Trails extends Model
{
    /**
     * Trails::$table
     *
     * @var string
     */
    public $table = 'sys_sessions_trails';

    // ------------------------------------------------------------------------

    /**
     * Trails::session
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function session()
    {
        return $this->belongsTo(Sessions::class);
    }
}