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

namespace O2System\Framework\Http\Message\Uri;

// ------------------------------------------------------------------------

use O2System\Spl\Exceptions\RuntimeException;

/**
 * Class Segments
 *
 * @package O2System\Framework\Http\Message\Uri
 */
class Segments
{
    protected $string;
    protected $parts;

    public function __construct( $string = null )
    {
        if ( is_null( $string ) ) {
            if(config()->offsetExists('uri')) {
                $protocol = strtoupper( config( 'uri' )->offsetGet( 'protocol' ) );
            }

            empty( $protocol ) && $protocol = 'REQUEST_URI';

            switch ( $protocol ) {
                case 'AUTO':
                case 'REQUEST_URI':
                    $string = $this->parseRequestUri();
                    break;
                case 'QUERY_STRING':
                    $string = $this->parseQueryString();
                    break;
                case 'PATH_INFO':
                default:
                    $string = isset( $_SERVER[ $protocol ] )
                        ? $_SERVER[ $protocol ]
                        : $this->parseRequestUri();
                    break;
            }

        } elseif ( is_array( $string ) ) {
            $string = implode( '/', $string );
        }

        $string = str_replace( [ '\\', '_' ], [ '/', '-' ], $string );
        $string = trim( remove_invisible_characters( $string, false ), '/' );
        $this->setParts( explode( '/', $string ) );
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
    protected function parseRequestUri()
    {
        if ( ! isset( $_SERVER[ 'REQUEST_URI' ], $_SERVER[ 'SCRIPT_NAME' ] ) ) {
            return '';
        }

        $uri = parse_url( $_SERVER[ 'REQUEST_URI' ] );
        $query = isset( $uri[ 'query' ] )
            ? $uri[ 'query' ]
            : '';
        $uri = isset( $uri[ 'path' ] )
            ? $uri[ 'path' ]
            : '';

        if ( isset( $_SERVER[ 'SCRIPT_NAME' ][ 0 ] ) ) {
            if ( strpos( $uri, $_SERVER[ 'SCRIPT_NAME' ] ) === 0 ) {
                $uri = (string)substr( $uri, strlen( $_SERVER[ 'SCRIPT_NAME' ] ) );
            } elseif ( strpos( $uri, dirname( $_SERVER[ 'SCRIPT_NAME' ] ) ) === 0 ) {
                $uri = (string)substr( $uri, strlen( dirname( $_SERVER[ 'SCRIPT_NAME' ] ) ) );
            }
        }

        // This section ensures that even on servers that require the URI to be in the query string (Nginx) a correct
        // URI is found, and also fixes the QUERY_STRING server var and $_GET array.
        if ( trim( $uri, '/' ) === '' AND strncmp( $query, '/', 1 ) === 0 ) {
            $query = explode( '?', $query, 2 );
            $uri = $query[ 0 ];

            $_SERVER[ 'QUERY_STRING' ] = isset( $query[ 1 ] )
                ? $query[ 1 ]
                : '';
        } else {
            $_SERVER[ 'QUERY_STRING' ] = $query;
        }

        if( isset( $_GET[ 'SEGMENTS_STRING' ] ) ) {
            $uri = $_GET[ 'SEGMENTS_STRING' ];
            unset( $_GET[ 'SEGMENTS_STRING' ] );

            $_SERVER[ 'QUERY_STRING' ] = str_replace([
                'SEGMENTS_STRING=' . $uri . '&',
                'SEGMENTS_STRING=' . $uri,
            ], '', $_SERVER[ 'QUERY_STRING' ] );
        }

        parse_str( $_SERVER[ 'QUERY_STRING' ], $_GET );

        if ( $uri === '/' || $uri === '' ) {
            return '/';
        }

        return $uri;
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
    protected function parseQueryString()
    {
        $uri = isset( $_SERVER[ 'QUERY_STRING' ] )
            ? $_SERVER[ 'QUERY_STRING' ]
            : @getenv( 'QUERY_STRING' );

        if ( trim( $uri, '/' ) === '' ) {
            return '';
        } elseif ( strncmp( $uri, '/', 1 ) === 0 ) {
            $uri = explode( '?', $uri, 2 );
            $_SERVER[ 'QUERY_STRING' ] = isset( $uri[ 1 ] )
                ? $uri[ 1 ]
                : '';
            $uri = rawurldecode( $uri[ 0 ] );
        }

        parse_str( $_SERVER[ 'QUERY_STRING' ], $_GET );

        return $uri;
    }

    // --------------------------------------------------------------------

    /**
     * Get String
     *
     * Get Requested Uri String
     *
     * @return string
     */
    public function getString()
    {
        return empty( $this->string )
            ? '/'
            : $this->string;
    }

    // ------------------------------------------------------------------------

    public function addString( $string )
    {
        $string = $this->string . '/' . trim( $string, '/' );

        return $this->withString( $string );
    }

    // ------------------------------------------------------------------------

    public function withString( $string )
    {
        $string = trim( remove_invisible_characters( $string, false ), '/' );

        return $this->withParts( explode( '/', $string ) );
    }

    public function withParts( array $parts )
    {
        $uri = clone $this;
        $uri->setParts( $parts );

        return $uri;
    }

    // ------------------------------------------------------------------------

    public function addParts( array $parts )
    {
        $parts = array_merge( $this->parts, $parts );

        return $this->withParts( $parts );
    }

    /**
     * Get Segment
     *
     * @param int $n (n) of Uri Segments
     *
     * @return mixed
     */
    public function getPart( $n )
    {
        return isset( $this->parts[ $n ] )
            ? $this->parts[ $n ]
            : false;
    }

    /**
     * Get Segments
     *
     * @return array
     */
    public function getParts()
    {
        return $this->parts;
    }

    // ------------------------------------------------------------------------

    protected function setParts( array $parts )
    {
        if ( count( $parts ) ) {
            $validSegments = [];

            if ( count( $parts ) ) {
                foreach ( $parts as $part ) {
                    // Filter segments for security
                    if ( $part = trim( $this->filterPart( $part ) ) ) {

                        if ( false !== ( $language = language()->packageExists( $part ) ) ) {
                            language()->setDefault( $part );

                            continue;
                        } elseif( ! in_array( $part, $validSegments ) ) {
                            $validSegments[] = $part;
                        }
                    }
                }
            }

            $validSegments = array_filter( $validSegments );
            array_unshift( $validSegments, null );

            unset( $validSegments[ 0 ] );

            $this->parts = $validSegments;
            $this->string = implode( '/', $this->parts );
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Has Segment
     *
     * @param string $part
     * @param bool   $isCaseSensitive
     *
     * @return bool
     */
    public function hasPart( $part, $isCaseSensitive = false )
    {
        return (bool)in_array( $part, $this->parts, $isCaseSensitive );
    }

    // ------------------------------------------------------------------------

    /**
     * Get Total Segments
     *
     * @return int
     */
    public function getTotalParts()
    {
        return count( $this->parts );
    }

    public function __toString()
    {
        if ( empty( $this->parts ) ) {
            return implode( '/', $this->parts );
        }

        return '';
    }

    /**
     * Filter Segment
     *
     * Filters segments for malicious characters.
     *
     * @param string $string URI String
     *
     * @return mixed
     * @throws RuntimeException
     */
    protected function filterPart( $string )
    {
        $config = config( 'uri' );

        if ( ! empty( $string ) AND
            ! empty( $config->offsetGet( 'permittedChars' ) ) AND
            ! preg_match( '/^[' . $config->offsetGet( 'permittedChars' ) . ']+$/i', $string ) AND
            ! is_cli()
        ) {
            throw new RuntimeException( 'E_URI_HAS_DISALLOWED_CHARACTERS', 105 );
        }

        $regex = [ '$', '(', ')', '%28', '%29', 'index' ]; // Bad
        $replace = [ '&#36;', '&#40;', '&#41;', '&#40;', '&#41;' ]; // Good

        if(! empty($config)) {
            array_push($regex, $config->offsetGet('suffix'));
            array_push($replace, '');
        }

        // Convert programatic characters to entities and return
        return str_replace( $regex, $replace, $string );
    }
}