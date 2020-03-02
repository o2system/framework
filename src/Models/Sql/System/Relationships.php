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
 * Class Relationships
 * @package O2System\Framework\Models\Sql\System
 */
class Relationships extends Model
{
    /**
     * Relationships::$table
     *
     * @var string
     */
    public $table = 'sys_relationships';

    // ------------------------------------------------------------------------

    /**
     * Relationships::items
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function ownership()
    {
        return $this->morphTo();
    }

    // ------------------------------------------------------------------------

    /**
     * Relationships::relation
     * 
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function relation()
    {
        return $this->morphTo();
    }
}