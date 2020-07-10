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
use O2System\Framework\Models\Sql\Traits\SettingsTrait;

/**
 * Class Modules
 * @package O2System\Framework\Models\Sql\System\Models
 */
class Modules extends Model
{
    use MetadataTrait;
    use SettingsTrait;
    use HierarchicalTrait;

    /**
     * Modules::$table
     *
     * @var string
     */
    public $table = 'sys_modules';

    /**
     * Modules::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        'settings'
    ];

    // ------------------------------------------------------------------------

    /**
     * Modules::roles
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function roles()
    {
        return $this->hasMany(Modules\Roles::class);
    }
}