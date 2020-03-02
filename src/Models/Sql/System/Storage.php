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
use O2System\Framework\Models\Sql\Traits\HierarchicalTrait;
use O2System\Framework\Models\Sql\Traits\MetadataTrait;

/**
 * Class Storage
 * @package O2System\Framework\Models\Sql\System
 */
class Storage extends Model
{
    use HierarchicalTrait;

    /**
     * Storage::$table
     *
     * @var string
     */
    public $table = 'sys_storage';

    /**
     * Storage::$uploadFilePaths
     *
     * @var array
     */
    public $uploadFilePaths = [
        'filepath' => PATH_STORAGE
    ];

    /**
     * Storage::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        'fileurl'
    ];

    // ------------------------------------------------------------------------

    /**
     * Storage::ownership
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function ownership()
    {
        return $this->morphTo();
    }

    // ------------------------------------------------------------------------

    public function fileurl()
    {
        if(is_file($filePath = $this->uploadFilePaths['filepath'] . $this->row->filepath)) {
            return storage_url($filePath);
        } else {
            return storage_url('images/not-found.jpg');
        }
    }
}