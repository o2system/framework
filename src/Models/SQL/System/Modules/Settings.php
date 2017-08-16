<?php
/**
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */
// ------------------------------------------------------------------------

namespace O2System\Framework\SQL\Models\Modules;

// ------------------------------------------------------------------------

use O2System\Orm\Abstracts\AbstractModel;

/**
 * Class Settings
 *
 * @package O2System\Framework\SQL\Models
 */
class Settings extends AbstractModel
{
    /**
     * Settings::$table
     *
     * System modules settings database table name.
     *
     * @var string
     */
    public $table = 'sys_modules_settings';
}