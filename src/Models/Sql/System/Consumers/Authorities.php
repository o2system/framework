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

namespace O2System\Framework\Models\Sql\System\Consumers;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\System\Consumers;

/**
 * Class Authorities
 * @package O2System\Framework\Models\Sql\System\Consumers
 */
class Authorities extends Model
{
    /**
     * Authorities::$table
     *
     * @var string
     */
    public $table = 'sys_consumers_authorities';

    // ------------------------------------------------------------------------

    /**
     * Authorities::consumer
     * 
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function consumer()
    {
        return $this->belongsTo(Consumers::class);
    }
}