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
namespace O2System\Framework\Http\Controllers\System;
// ------------------------------------------------------------------------

use O2System\Framework\Http\Controllers\Restful;
use App\Api\Http\Controller;
// ------------------------------------------------------------------------
/**
 * Class Migrations
 * @package O2System\Framework\Http\Controllers\System
 */
class Migrations extends Restful
{
    /**
     * Migrations::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Migrations';


}
