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

namespace O2System\Framework\Models\Sql\System\Storage;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\System\Storage;

/**
 * Class Authorities
 * @package O2System\Framework\Models\Sql\System\Storage
 */
class Authorities extends Model
{
    /**
     * Authorities::$table
     *
     * @var string
     */
    public $table = 'sys_storage_authorities';

    // ------------------------------------------------------------------------

    /**
     * Authorities::storage
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function storage()
    {
        return $this->belongsTo(Storage::class);
    }

    // ------------------------------------------------------------------------

    /**
     * Authorities::authority
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function authority()
    {
        return $this->morphTo();
    }
}