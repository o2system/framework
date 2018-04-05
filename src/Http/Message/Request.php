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

use O2System\Framework\Http\Router\Datastructures\Controller;
use O2System\Kernel\Http\Message;
use Traversable;

/**
 * Class Request
 *
 * @package O2System\Framework\Http\Message
 */
class Request extends Message\Request implements \IteratorAggregate
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
        if( false !== ( $controller = services( 'controller' ) ) ) {
            return $controller;
        }

        return false;
    }

    //--------------------------------------------------------------------

    /**
     * Retrieve an external iterator
     *
     * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     *        <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator( $_REQUEST );
    }

    //--------------------------------------------------------------------

    /**
     * Count elements of an object
     *
     * @link  http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     *        </p>
     *        <p>
     *        The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count( $_REQUEST );
    }
}