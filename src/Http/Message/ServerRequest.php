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

namespace O2System\Framework\Http\Message;

// ------------------------------------------------------------------------

use O2System\Framework\DataStructures\Input\Files;
use O2System\Kernel\Http\Message;
use O2System\Kernel\Http\Router\DataStructures\Controller;

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
     * ServerRequest::__construct
     *
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     */
    public function __construct()
    {
        parent::__construct();
        
        $uploadedFiles = new Files();
        $uploadedFiles->exchangeArray($this->uploadedFiles->getArrayCopy());
        
        $this->uploadedFiles = $uploadedFiles;
    }

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