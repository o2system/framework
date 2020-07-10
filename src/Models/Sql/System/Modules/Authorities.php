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

namespace O2System\Framework\Models\Sql\System\Modules;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\System\Modules;

/**
 * Class Authorities
 * @package O2System\Framework\Models\Sql\System\Modules
 */
class Authorities extends Model
{
   /**
     * Authorities::$table
     *
     * @var string
     */
    public $table = 'sys_modules_authorities';

    // ------------------------------------------------------------------------

    /**
     * Authorities::module
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function module()
    {
        return $this->belongsTo(Modules::class);
    }

    // ------------------------------------------------------------------------

    /**
     * Actions::ownership
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function ownership()
    {
        return $this->morphTo();
    }
}