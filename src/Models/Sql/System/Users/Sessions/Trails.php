<?php
/**
 * This file is part of the NEO ERP Application.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @author         PT. Lingkar Kreasi (Circle Creative)
 *  @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */

// ------------------------------------------------------------------------

namespace O2System\Framework\Models\Sql\System\Users\Sessions;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\System\Users\Sessions;

/**
 * Class Trails
 * @package O2System\Framework\Models\Sql\System\Users\Sessions
 */
class Trails extends Model
{
    /**
     * Trails::$table
     *
     * @var string
     */
    public $table = 'sys_users_sessions_trails';

    // ------------------------------------------------------------------------

    /**
     * Trails::user
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function session()
    {
        return $this->belongsTo(Sessions::class);
    }
}