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
    /**
     * Uri String
     *
     * String is part after SCRIPT_FILENAME
     *
     * index.php/controller/method/params
     *
     * @var string
     */
    protected $string;

    /**
     * Uri Segments
     *
     * Segments is list of string parts after SCRIPT_FILENAME
     *
     * @var array
     */
    protected $segments = [ ];

    public function __construct ()
    {
        parent::__construct();

        $uriString = null;
        $protocol = strtoupper( config( 'uri' )->offsetGet( 'protocol' ) );

        empty( $protocol ) && $protocol = 'REQUEST_URI';

        switch ( $protocol ) {
            case 'AUTO':
            case 'REQUEST_URI':
                $uriString = $this->parseRequestURI();
                break;
            case 'QUERY_STRING':
                $uriString = $this->parseQueryString();
                break;
            case 'PATH_INFO':
            default:
                $uriString = isset( $_SERVER[ $protocol ] )
                    ? $_SERVER[ $protocol ]
                    : $this->parseRequestURI();
                break;
        }

        // Filter out control characters and trim slashes
        $uriString = trim( remove_invisible_characters( $uriString, false ), '/' );
        $this->setSegments( explode( '/', $uriString ) );
    }

    /**
     * Parse REQUEST_URI
     *
     * Will parse REQUEST_URI and automatically detect the URI from it,
     * while fixing the query string if necessary.
     *
     * @access  protected
     * @return  string
     */
    protected function parseRequestURI ()
    {
        if ( ! isset( $_SERVER[ 'REQUEST_URI' ], $_SERVER[ 'SCRIPT_NAME' ] ) ) {
            return '';
        }

        $uri = parse_url( $_SERVER[ 'REQUEST_URI' ] );
        $query = isset( $uri[ 'query' ] ) ? $uri[ 'query' ] : '';
        $uri = isset( $uri[ 'path' ] ) ? $uri[ 'path' ] : '';

        if ( isset( $_SERVER[ 'SCRIPT_NAME' ][ 0 ] ) ) {
            if ( strpos( $uri, $_SERVER[ 'SCRIPT_NAME' ] ) === 0 ) {
                $uri = (string) substr( $uri, strlen( $_SERVER[ 'SCRIPT_NAME' ] ) );
            } elseif ( strpos( $uri, dirname( $_SERVER[ 'SCRIPT_NAME' ] ) ) === 0 ) {
                $uri = (string) substr( $uri, strlen( dirname( $_SERVER[ 'SCRIPT_NAME' ] ) ) );
            }
        }

        // This section ensures that even on servers that require the URI to be in the query string (Nginx) a correct
        // URI is found, and also fixes the QUERY_STRING server var and $_GET array.
        if ( trim( $uri, '/' ) === '' AND strncmp( $query, '/', 1 ) === 0 ) {
            $query = explode( '?', $query, 2 );
            $uri = $query[ 0 ];

            $_SERVER[ 'QUERY_STRING' ] = isset( $query[ 1 ] ) ? $query[ 1 ] : '';
        } else {
            $_SERVER[ 'QUERY_STRING' ] = $query;
        }

        parse_str( $_SERVER[ 'QUERY_STRING' ], $_GET );

        if ( $uri === '/' || $uri === '' ) {
            return '/';
        }

        // Do some final cleaning of the URI and return it
        return $this->removeRelativeDirectory( $uri );
    }

    /**
     * Remove relative directory (../) and multi slashes (///)
     *
     * Do some final cleaning of the URI and return it, currently only used in self::parseRequestURI()
     *
     * @param   string $uri URI String
     *
     * @access  protected
     * @return  string
     */
    protected function removeRelativeDirectory ( $uri )
    {
        $segments = [ ];
        $segment = strtok( $uri, '/' );

        $base_dirs = explode( '/', str_replace( '\\', '/', PATH_ROOT ) );

        while ( $segment !== false ) {
            if ( ( ! empty( $segment ) || $segment === '0' ) AND
                 $segment !== '..' AND
                 ! in_array(
                     $segment,
                     $base_dirs
                 )
            ) {
                $segments[] = $segment;
            }
            $segment = strtok( '/' );
        }

        return implode( '/', $segments );
    }

    // ------------------------------------------------------------------------

    /**
     * Parse QUERY_STRING
     *
     * Will parse QUERY_STRING and automatically detect the URI from it.
     *
     * @access  protected
     * @return  string
     */
    protected function parseQueryString ()
    {
        $uri = isset( $_SERVER[ 'QUERY_STRING' ] ) ? $_SERVER[ 'QUERY_STRING' ] : @getenv( 'QUERY_STRING' );

        if ( trim( $uri, '/' ) === '' ) {
            return '';
        } elseif ( strncmp( $uri, '/', 1 ) === 0 ) {
            $uri = explode( '?', $uri, 2 );
            $_SERVER[ 'QUERY_STRING' ] = isset( $uri[ 1 ] ) ? $uri[ 1 ] : '';
            $uri = rawurldecode( $uri[ 0 ] );
        }

        parse_str( $_SERVER[ 'QUERY_STRING' ], $_GET );

        return $this->removeRelativeDirectory( $uri );
    }

    // --------------------------------------------------------------------

    /**
     * Get String
     *
     * Get Requested Uri String
     *
     * @return string
     */
    public function getString ()
    {
        return empty( $this->string ) ? '/' : $this->string;
    }

    // ------------------------------------------------------------------------

    public function setString ( $string )
    {
        // Filter out control characters
        $string = remove_invisible_characters( $string, false );
        $this->setSegments( explode( '/', $string ) );
    }

    // ------------------------------------------------------------------------

    public function withSegment ( $segment )
    {
        return $this->withSegments( explode( '/', $segment ) );
    }

    // ------------------------------------------------------------------------

    public function withSegments ( array $segments )
    {
        $uri = clone $this;

        if ( count( $segments ) ) {
            $uri->setSegments( $segments );
        }

        return $uri;
    }

    // ------------------------------------------------------------------------

    /**
     * Get Segment
     *
     * @param int $n (n) of Uri Segments
     *
     * @return mixed
     */
    public function getSegment ( $n )
    {
        return isset( $this->segments[ $n ] ) ? $this->segments[ $n ] : false;
    }

    /**
     * Get Segments
     *
     * @return array
     */
    public function getSegments ()
    {
        return $this->segments;
    }

    // ------------------------------------------------------------------------

    public function setSegments ( array $segments )
    {
        $validSegments = [ ];

        if ( count( $segments ) ) {
            foreach ( $segments as $key => $segment ) {
                // Filter segments for security
                if ( $segment = trim( $this->filterSegment( $segment ) ) ) {
                    if ( false !== ( $language = language()->isPackageExists( $segment ) ) ) {
                        language()->setDefault( $segment );

                        continue;
                    } else {
                        $validSegments[] = $segment;
                    }
                }
            }
        }

        $validSegments = array_filter( $validSegments );
        array_unshift( $validSegments, null );

        unset( $validSegments[ 0 ] );

        $this->segments = $validSegments;
        $this->string = implode( '/', $this->segments );
    }

    // ------------------------------------------------------------------------

    /**
     * Has Segment
     *
     * @param string $segment
     * @param bool   $isCaseSensitive
     *
     * @return bool
     */
    public function hasSegment ( $segment, $isCaseSensitive = false )
    {
        return (bool) in_array( $segment, $this->segments, $isCaseSensitive );
    }

    // ------------------------------------------------------------------------

    /**
     * Get Total Segments
     *
     * @return int
     */
    public function getTotalSegments ()
    {
        return count( $this->segments );
    }

    // ------------------------------------------------------------------------

    public function __toString ()
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

        $uriString .= empty( $this->path ) ? '' : '/' . $this->path;
        $uriString .= empty( $this->string ) ? '' : '/' . $this->string;

        // add uri suffix
        if ( $uriSuffix = config( 'uri' )->offsetGet( 'suffix' ) ) {
            $uriString .= pathinfo( $uriString, PATHINFO_EXTENSION ) === '' ? $uriSuffix : '';
        }

        $uriString .= empty( $this->query ) ? '' : '/?' . $this->query;
        $uriString .= empty( $this->fragment ) ? '' : $this->fragment;

        return $uriString;
    }

    // ------------------------------------------------------------------------

    /**
     * Filter Segment
     *
     * Filters segments for malicious characters.
     *
     * @param string $segment URI String
     *
     * @return mixed
     * @throws HttpRequestUriException
     */
    protected function filterSegment ( $segment )
    {
        $config = config( 'uri' );

        if ( ! empty( $segment ) AND
             ! empty( $config->offsetGet( 'permittedChars' ) ) AND
             ! preg_match( '/^[' . $config->offsetGet( 'permittedChars' ) . ']+$/i', $segment ) AND
             ! is_cli()
        ) {
            throw new HttpRequestUriException( 'E_URI_HAS_DISALLOWED_CHARACTERS', 105 );
        }

        // Convert programatic characters to entities and return
        return str_replace(
            [ '$', '(', ')', '%28', '%29', $config->offsetGet( 'suffix' ) ],    // Bad
            [ '&#36;', '&#40;', '&#41;', '&#40;', '&#41;', '' ],    // Good
            $segment
        );
    }
}