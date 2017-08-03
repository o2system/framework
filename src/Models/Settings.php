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

namespace O2System\Framework\Models;

// ------------------------------------------------------------------------

use O2System\Orm\Abstracts\AbstractModel;

/**
 * Class Settings
 *
 * @package O2System\Framework\Models
 */
class Settings extends AbstractModel
{
    /**
     * Settings::$table
     *
     * O2System Framework Modules Settings Table Name.
     *
     * @var string
     */
    public $table = 'sys_modules_settings';
}