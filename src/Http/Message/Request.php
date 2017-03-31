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
use O2System\Psr\Http\Message\UriInterface;
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

    public function __construct()
    {
        $this->uri = new Uri();
    }

    /**
     * RequestInterface::getUri
     *
     * Retrieves the URI instance.
     *
     * This method MUST return a UriInterface instance.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.3
     * @return Uri Returns a UriInterface instance
     *     representing the URI of the request.
     */
    public function &getUri()
    {
        if ( empty( $this->uri ) ) {
            $this->uri = new Uri();
        }

        return $this->uri;
    }

    // ------------------------------------------------------------------------

    public function getClientIpAddress()
    {
        return input()->ipAddress( config()->getItem( 'ipAddresses' )->proxy );
    }

    // ------------------------------------------------------------------------

    /**
     * Determines if this request was made from the command line (CLI).
     *
     * @return bool
     */
    public function isCLI()
    {
        return ( PHP_SAPI === 'cli' || defined( 'STDIN' ) );
    }

    // ------------------------------------------------------------------------

    /**
     * Test to see if a request contains the HTTP_X_REQUESTED_WITH header.
     *
     * @return bool
     */
    public function isAJAX()
    {
        return ( ! empty( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) &&
            strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) === 'xmlhttprequest' );
    }

    //--------------------------------------------------------------------

    /**
     * Attempts to detect if the current connection is secure through
     * a few different methods.
     *
     * @return bool
     */
    public function isSecure()
    {
        if ( ! empty( $_SERVER[ 'HTTPS' ] ) && strtolower( $_SERVER[ 'HTTPS' ] ) !== 'off' ) {
            return true;
        } elseif ( isset( $_SERVER[ 'HTTP_X_FORWARDED_PROTO' ] ) && $_SERVER[ 'HTTP_X_FORWARDED_PROTO' ] === 'https' ) {
            return true;
        } elseif ( ! empty( $_SERVER[ 'HTTP_FRONT_END_HTTPS' ] ) && strtolower(
                $_SERVER[ 'HTTP_FRONT_END_HTTPS' ]
            ) !== 'off'
        ) {
            return true;
        }

        return false;
    }

    //--------------------------------------------------------------------

    /**
     * Request::getTime
     *
     * @param string $format
     *
     * @return bool|mixed|string
     */
    public function getTime( $format = null )
    {
        return isset( $format )
            ? date( $format, $_SERVER[ 'REQUEST_TIME' ] )
            : $_SERVER[ 'REQUEST_TIME' ];
    }

    //--------------------------------------------------------------------

    /**
     * Request::getServer
     *
     * @return \O2System\Kernel\Http\Message\ServerRequest
     */
    public function getServer()
    {
        static $serverRequest;

        if ( empty( $serverRequest ) ) {
            $serverRequest = new Message\ServerRequest();
        }

        return $serverRequest;
    }

    //--------------------------------------------------------------------

    /**
     * Request::getLanguage
     *
     * @return string
     */
    public function getLanguage()
    {
        return 'en-US';
    }

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

    //--------------------------------------------------------------------

    public function setUri( UriInterface $uri )
    {
        $this->uri = $uri;
    }

    /**
     * Request::withUri
     *
     * @param \O2System\Psr\Http\Message\UriInterface $uri
     * @param bool                                    $preserveHost
     *
     * @return \O2System\Framework\Http\Message\Request|static
     */
    public function withUri( UriInterface $uri, $preserveHost = false )
    {
        $request = clone $this;
        $request->uri = $uri;

        if ( $preserveHost ) {
            if ( null !== ( $host = $uri->getHost() ) ) {
                if ( null !== ( $port = $uri->getPort() ) ) {
                    $host .= ':' . $port;
                }

                return $request->withHeader( 'Host', $host );
            }
        }

        return $request;
    }
}