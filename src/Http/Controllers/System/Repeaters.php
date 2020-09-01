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
namespace O2System\Framework\Http\Controllers\System;

// ------------------------------------------------------------------------
use O2System\Framework\Http\Controllers\Restful;
// ------------------------------------------------------------------------
/**
 * Class Repeaters
 * @package O2System\Framework\Http\Controllers\System
 */
class Repeaters extends Restful
{
    /**
     * Repeaters::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Repeaters';

    // ------------------------------------------------------------------------

    /**
     * Repeaters::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
        'repeat_minute',
        'repeat_hour',
        'repeat_day',
        'repeat_date',
        'repeat_week',
        'repeat_month',
        'repeat_year',
        'date_start',
        'date_end',
        'time_start',
        'time_end',
        'until',
    ];

}
