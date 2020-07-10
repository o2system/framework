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
use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class Settings
 * @package O2System\Framework\Models\Sql\System
 */
class Settings extends Model
{
    /**
     * Settings::$table
     *
     * @var string
     */
    public $table = 'sys_settings';

    // ------------------------------------------------------------------------

    /**
     * Settings::ownership
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function ownership()
    {
        return $this->morphTo();
    }

    public function fetch(): SplArrayObject
    {
        $metadata = new SplArrayObject();

        if ($result = $this->all()) {
            foreach ($result as $row) {
                $metadata->offsetSet($row->key, $row->value);
            }
        }
        return $metadata;
    }
}