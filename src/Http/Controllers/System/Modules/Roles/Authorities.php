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
namespace O2System\Framework\Http\Controllers\System\Roles;


use O2System\Framework\Http\Controllers\Restful;

/**
 * Class Authorities
 * @package O2System\Framework\Http\Controllers\System\Roles
 */
class Authorities extends Restful
{
    /**
     * Authorities::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Modules\Roles\Authorities';

}
