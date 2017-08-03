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

namespace O2System\Framework\Models\Modules;

// ------------------------------------------------------------------------

use O2System\Orm\Abstracts\AbstractModel;

/**
 * Class Navigations
 *
 * @package O2System\Framework\Models
 */
class Navigations extends AbstractModel
{
    /**
     * Navigations::$table
     *
     * System modules navigations database table name.
     *
     * @var string
     */
    public $table = 'sys_modules_navigations';
}