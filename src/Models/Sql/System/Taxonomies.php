<?php


namespace O2System\Framework\Models\Sql\System;


use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\HierarchicalTrait;

class Taxonomies extends Model
{
    use HierarchicalTrait;

    /**
     * Taxonomies::$table
     *
     * @var string
     */
    public $table = 'sys_taxonomies';

    /**
     * Posts::$fillableColumns
     *
     * @var array
     */
    public $fillableColumns = [
        'id',
        'id_parent',
        'name',
        'slug',
        'logo',
        'code',
        'description',
        'record_status',
        'record_create_timestamp'
    ];

}
