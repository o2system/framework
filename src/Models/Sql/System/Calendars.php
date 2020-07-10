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


use O2System\Framework\Models\Sql\Model;

/**
 * Class Calendars
 * @package O2System\Framework\Models\Sql\System
 */
class Calendars extends Model
{
    /**
     * Calendars::$table
     *
     * @var string
     */
    public $table = 'sys_calendars';

    /**
     * Calendars::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
    ];

    // ------------------------------------------------------------------------

    /**
     * Calendars::ownership
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function ownership()
    {
        return $this->morphTo();
    }
}