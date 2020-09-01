<?php


namespace O2System\Framework\Http\Controllers\System;


use O2System\Framework\Http\Controllers\Restful;

class Calendars extends Restful
{
    /**
     * Calendars::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Calendars';

    // ------------------------------------------------------------------------

    /**
     * Calendars::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
        'datetime_start',
        'datetime_end',
        'record_type'
    ];
}
