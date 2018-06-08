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

namespace O2System\Framework\Http\Message;

// ------------------------------------------------------------------------

use O2System\Kernel\Http\Message;
use O2System\Kernel\Http\Router\Datastructures\Controller;

/**
 * Class ServerRequest
 *
 * @package O2System\Framework\Http\Message
 */
class ServerRequest extends Message\ServerRequest implements \IteratorAggregate
{
    /**
     * Request::$controller
     *
     * Requested Controller FilePath
     *
     * @var string Controller FilePath.
     */
    protected $controller;

    // ------------------------------------------------------------------------

    /**
     * Request::getLanguage
     *
     * @return string
     */
    public function getLanguage()
    {
        return language()->getDefault();
    }

    //--------------------------------------------------------------------

    /**
     * Request::getController
     *
     * @return bool|Controller
     */
    public function getController()
    {
        if (false !== ($controller = services('controller'))) {
            return $controller;
        }

        return false;
    }
}