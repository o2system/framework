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
namespace O2System\Framework\Models\Sql\System;

// ------------------------------------------------------------------------
use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\HierarchicalTrait;
// ------------------------------------------------------------------------
/**
 * Class Taxonomies
 * @package O2System\Framework\Models\Sql\System
 */
class Taxonomies extends Model
{
    use HierarchicalTrait;

    /**
     * Taxonomies::$table
     *
     * @var string
     */
    public $table = 'sys_taxonomies';

    // ------------------------------------------------------------------------

    /**
     * Taxonomies::$fillableColumns
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

    // ------------------------------------------------------------------------
    /**
     * Taxonomies::$insertValidationRules
     *
     * @var array
     */
    public $insertValidationRules = [
        'id_parent' => 'required',
        'name' => 'required',
        'slug' => 'required',
        'description' => 'optional'
    ];

    // ------------------------------------------------------------------------
    /**
     * Taxonomies::$insertValidationCustomErrors
     *
     * @var array
     */
    public $insertValidationCustomErrors = [
        'id_parent' => [
            'required' => 'Parent id cannot be empty!',
            'integer' => 'Parent id data must be an integer'
        ],
        'name' => [
            'required' => 'Taxonomy name cannot be empty!'
        ],
        'slug' => [
            'required' => 'Taxonomy slug cannot be empty!'
        ]
    ];

    // ------------------------------------------------------------------------

    /**
     * Taxonomies::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'id_parent' => 'required',
        'name' => 'required',
        'slug' => 'required',
        'description' => 'optional'
    ];

    // ------------------------------------------------------------------------

    /**
     * Taxonomies::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Taxonomy id cannot be empty!',
            'integer' => 'Taxonomy id data must be an integer'
        ],
        'id_parent' => [
            'required' => 'Parent id cannot be empty!',
            'integer' => 'Parent id data must be an integer'
        ],
        'name' => [
            'required' => 'Taxonomy name cannot be empty!'
        ],
        'slug' => [
            'required' => 'Taxonomy slug cannot be empty!'
        ]
    ];

    // ------------------------------------------------------------------------

    /**
     * Taxonomies::image
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function image()
    {
        if ($result = $this->morphToManyThrough(Media::class, Relationships::class, 'relation')) {
            if ($result->count()) {
                return $result[0]->filepath;
            }
        }

        return 'https://via.placeholder.com/300';
    }

}
