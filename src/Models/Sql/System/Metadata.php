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

/**
 * Class Metadata
 * @package O2System\Framework\Models\Sql\System
 */
class Metadata extends Model
{
    /**
     * Metadata::$table
     *
     * @var string
     */
    public $table = 'sys_metadata';

    // ------------------------------------------------------------------------

    /**
     * Metadata::ownership
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function ownership()
    {
        return $this->morphTo();
    }
}