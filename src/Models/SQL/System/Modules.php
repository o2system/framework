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

namespace O2System\Framework\SQL\Models;

// ------------------------------------------------------------------------

use O2System\Orm\Abstracts\AbstractModel;

/**
 * Class Modules
 *
 * @package O2System\Framework\SQL\Models
 */
class Modules extends AbstractModel
{
    /**
     * Modules::$table
     *
     * System modules database table name.
     *
     * @var string
     */
    public $table = 'sys_modules';
}