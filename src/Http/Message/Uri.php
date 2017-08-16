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

use O2System\Framework\Http\Message\Uri\Segments;
use O2System\Kernel\Http\Message;

/**
 * Class Uri
 *
 * Based on PSR7 Psr\Http\Message\UriInterface.
 * The following are two example URIs and their component parts:
 *
 * foo://example.com:8042/over/there?name=ferret#nose
 * \_/   \______________/\_________/ \_________/ \__/
 *  |           |            |            |        |
 * scheme   authority       path        query   fragment
 *  |   _____________________|__
 * / \ /                        \
 * urn:example:animal:ferret:nose
 *
 * @see     http://tools.ietf.org/html/rfc3986 (the URI specification)
 *
 * @package O2System\Framework\Http\Message
 */
class Uri extends Message\Uri
{
    protected $segments;
    protected $suffix;

    public function __construct( $httpStringRequest = null )
    {
        parent::__construct( $httpStringRequest );

        if( isset( $httpStringRequest ) ) {
            $this->segments = new Segments( '' );
        } else {
            $this->segments = new Segments();
        }

        $this->setSuffix( config( 'uri' )->offsetGet( 'suffix' ) );
    }

    // ------------------------------------------------------------------------

    public function addPath( $path )
    {
        $uri = clone $this;
        $uri->path .= '/' . ltrim( $path, '/' );

        return $uri;
    }

    /**
     * Uri::getSegments
     *
     * @return Segments
     */
    public function &getSegments()
    {
        return $this->segments;
    }

    // ------------------------------------------------------------------------

    /**
     * Uri::withSegments
     *
     * @param Segments $segments
     *
     * @return Uri
     */
    public function withSegments( Segments $segments )
    {
        $uri = clone $this;
        $uri->segments = $segments;

        return $uri;
    }

    // ------------------------------------------------------------------------

    public function addSegments( $segments )
    {
        if( ! $segments instanceof Segments ) {
            $segments = new Segments( $segments );
        }

        $currentSegments = $this->segments->getParts();
        $addSegments = $segments->getParts();

        $uri = clone $this;
        $uri->segments = new Segments( array_merge( $currentSegments, $addSegments ) );

        return $uri;
    }

    public function addQuery( $query )
    {
        $uri = clone $this;
        $query = is_array( $query ) ? http_build_query( $query, PHP_QUERY_RFC3986 ) : $query;

        parse_str( $query, $newQuery );

        if( ! empty( $uri->query ) ) {
            parse_str( $uri->query, $oldQuery );
            $query = array_merge( $oldQuery, $newQuery );
        } else {
            $query = $newQuery;
        }

        if( is_array( $query ) ) {
            $uri->query = http_build_query( $query, PHP_QUERY_RFC3986 );
        }

        return $uri;
    }

    public function getSuffix()
    {
        return $this->suffix;
    }

    protected function setSuffix( $suffix )
    {
        if ( is_null( $suffix ) or is_bool( $suffix ) ) {
            $this->suffix = null;
        } elseif ( $suffix === '/' ) {
            $this->suffix = $suffix;
        } else {
            $this->suffix = '.' . trim( $suffix, '.' );
        }
    }

    public function withSuffix( $suffix )
    {
        $uri = clone $this;
        $uri->setSuffix( $suffix );

        return $uri;
    }

    /**
     * Uri::__toString
     *
     * @return string
     */
    public function __toString()
    {
        $uriString = $this->scheme . '://';

        if ( empty( $this->subDomains ) ) {
            $uriString .= $this->host;
        } else {
            $uriString .= implode( '.', $this->subDomains ) . '.' . $this->host;
        }

        if ( ! in_array( $this->port, [ 80, 443 ] ) ) {
            $uriString .= ':' . $this->port;
        }

        $uriPath = empty( $this->path )
            ? ''
            : '/' . $this->path;

        $uriPath .= empty( $this->string )
            ? ''
            : '/' . $this->string;

        $uriPath .= $this->segments->getTotalParts() == 0
            ? ''
            : '/' . $this->segments->getString();

        $uriPath = '/' . trim( $uriPath, '/' );

        if ( $uriPath !== '/' &&
            $this->suffix !== '' &&
            ( $uriPath . '/' !== $_SERVER['REQUEST_URI'] ) &&
            pathinfo( $uriPath, PATHINFO_EXTENSION ) === '' &&
            strpos( $uriPath, '#' ) === false &&
            empty( $this->query )
        ) {
            $uriPath .= $this->suffix;
        } elseif( pathinfo( $uriPath, PATHINFO_EXTENSION ) === '' ) {
            $uriPath .= '/';
        }

        $uriString .= str_replace( '//', '/', $uriPath );
        $uriString .= empty( $this->query )
            ? ''
            : '/?' . $this->query;
        $uriString .= empty( $this->fragment )
            ? ''
            : $this->fragment;

        return $uriString;
    }
}