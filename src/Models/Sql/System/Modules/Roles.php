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

namespace O2System\Framework\Models\Sql\System\Modules;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\System\Modules;

/**
 * Class Roles
 * @package O2System\Framework\Models\Sql\System\Modules
 */
class Roles extends Model
{
    /**
     * Roles::$table
     *
     * @var string
     */
    public $table = 'sys_modules_roles';

    // ------------------------------------------------------------------------

    /**
     * Roles::module
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function module()
    {
        return $this->belongsTo(Modules::class);
    }

    // ------------------------------------------------------------------------

    /**
     * Roles::authorities
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function authorities()
    {
        models(Modules\Roles\Authorities::class)->appendColumns = [
            'role',
            'endpoint'
        ];

        return $this->hasMany(Modules\Roles\Authorities::class);
    }
}