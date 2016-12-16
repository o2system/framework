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
use O2System\Security\Filters\Validation;
use Traversable;

/**
 * Class Request
 *
 * @package O2System\Framework\Http\Message
 */
class Request extends Message\Request implements \IteratorAggregate
{
    /**
     * Request::$clientIpAddress
     *
     * Client IPv4 Address.
     *
     * @var string IPv4 Address.
     */
    protected $clientIpAddress;

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
    public function &getUri ()
    {
        if ( empty( $this->uri ) ) {
            $this->uri = new Uri();
        }

        return $this->uri;
    }

    // ------------------------------------------------------------------------

    public function getClientIpAddress ()
    {
        $proxyIps = config( 'ipAddresses' )->offsetGet( 'proxy' );

        if ( ! empty( $proxyIps ) && ! is_array( $proxyIps ) ) {
            $proxyIps = array_map( 'trim', explode( ',', $proxyIps ) );
        }

        $this->clientIpAddress = $_SERVER[ 'REMOTE_ADDR' ];

        if ( $proxyIps ) {
            foreach ( [
                          'HTTP_CLIENT_IP', 'HTTP_X_CLIENT_IP', 'HTTP_FORWARDED', 'HTTP_X_FORWARDED', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_CLUSTER_CLIENT_IP', 'REMOTE_ADDR',
                      ] as $offset ) {
                $spoof = isset( $_SERVER[ $offset ] ) ? $_SERVER[ $offset ] : null;
                $spoof = empty( $spoof ) ? getenv( $offset ) : $spoof;

                if ( $spoof !== null ) {
                    // Some proxies typically list the whole chain of IP
                    // addresses through which the client has reached us.
                    // e.g. client_ip, proxy_ip1, proxy_ip2, etc.
                    sscanf( $spoof, '%[^,]', $spoof );

                    if ( ! Validation::isValidIp( $spoof ) ) {
                        $spoof = null;
                    } else {
                        break;
                    }
                }
            }

            if ( $spoof ) {
                for ( $i = 0, $c = count( $proxyIps ); $i < $c; $i++ ) {
                    // Check if we have an IP address or a subnet
                    if ( strpos( $proxyIps[ $i ], '/' ) === false ) {
                        // An IP address (and not a subnet) is specified.
                        // We can compare right away.
                        if ( $proxyIps[ $i ] === $this->clientIpAddress ) {
                            $this->clientIpAddress = $spoof;
                            break;
                        }

                        continue;
                    }

                    // We have a subnet ... now the heavy lifting begins
                    isset( $separator ) || $separator = Validation::isValidIp(
                        $this->clientIpAddress,
                        'ipv6'
                    ) ? ':' : '.';

                    // If the proxy entry doesn't match the IP protocol - skip it
                    if ( strpos( $proxyIps[ $i ], $separator ) === false ) {
                        continue;
                    }

                    // Convert the REMOTE_ADDR IP address to binary, if needed
                    if ( ! isset( $ip, $sprintf ) ) {
                        if ( $separator === ':' ) {
                            // Make sure we're have the "full" IPv6 format
                            $ip = explode(
                                ':',
                                str_replace(
                                    '::',
                                    str_repeat( ':', 9 - substr_count( $this->clientIpAddress, ':' ) ),
                                    $this->clientIpAddress
                                )
                            );

                            for ( $i = 0; $i < 8; $i++ ) {
                                $ip[ $i ] = intval( $ip[ $i ], 16 );
                            }

                            $sprintf = '%016b%016b%016b%016b%016b%016b%016b%016b';
                        } else {
                            $ip = explode( '.', $this->clientIpAddress );
                            $sprintf = '%08b%08b%08b%08b';
                        }

                        $ip = vsprintf( $sprintf, $ip );
                    }

                    // Split the netmask length off the network address
                    sscanf( $proxyIps[ $i ], '%[^/]/%d', $netaddr, $masklen );

                    // Again, an IPv6 address is most likely in a compressed form
                    if ( $separator === ':' ) {
                        $netaddr = explode(
                            ':',
                            str_replace( '::', str_repeat( ':', 9 - substr_count( $netaddr, ':' ) ), $netaddr )
                        );
                        for ( $i = 0; $i < 8; $i++ ) {
                            $netaddr[ $i ] = intval( $netaddr[ $i ], 16 );
                        }
                    } else {
                        $netaddr = explode( '.', $netaddr );
                    }

                    // Convert to binary and finally compare
                    if ( strncmp( $ip, vsprintf( $sprintf, $netaddr ), $masklen ) === 0 ) {
                        $this->clientIpAddress = $spoof;
                        break;
                    }
                }
            }
        }

        if ( ! Validation::isValidIp( $this->clientIpAddress ) ) {
            return $this->clientIpAddress = '0.0.0.0';
        } elseif( $this->clientIpAddress === '::1') {
            return $this->clientIpAddress = '127.0.0.1';
        }

        return $this->clientIpAddress;
    }

    // ------------------------------------------------------------------------

    /**
     * Determines if this request was made from the command line (CLI).
     *
     * @return bool
     */
    public function isCLI ()
    {
        return ( PHP_SAPI === 'cli' || defined( 'STDIN' ) );
    }

    // ------------------------------------------------------------------------

    /**
     * Test to see if a request contains the HTTP_X_REQUESTED_WITH header.
     *
     * @return bool
     */
    public function isAJAX ()
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
    public function isSecure ()
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
    public function getTime ( $format = null )
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
    public function getServer ()
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
    public function getLanguage ()
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
    public function getIterator ()
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
    public function count ()
    {
        return count( $_REQUEST );
    }

    /**
     * Request::withUri
     *
     * @param \O2System\Psr\Http\Message\UriInterface $uri
     * @param bool                                    $preserveHost
     *
     * @return \O2System\Framework\Http\Message\Request|static
     */
    public function withUri ( UriInterface $uri, $preserveHost = false )
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