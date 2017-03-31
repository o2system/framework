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

/**
 * Class Domain
 *
 * @package O2System\Kernel\Http\Message
 */
class Domain
{
    protected $string;
    protected $origin;
    protected $scheme = 'http';
    protected $www = false;
    protected $port = 80;
    protected $parentDomain = null;
    protected $subDomains = [];
    protected $tlds = [];
    protected $path;

    public function __construct( $string = null )
    {
        $this->origin = isset( $_SERVER[ 'HTTP_HOST' ] )
            ? $_SERVER[ 'HTTP_HOST' ]
            : $_SERVER[ 'SERVER_NAME' ];
        $this->scheme = is_https()
            ? 'https'
            : 'http';

        $paths = explode( '.php', $_SERVER[ 'PHP_SELF' ] );
        $paths = explode( '/', trim( $paths[ 0 ], '/' ) );
        array_pop( $paths );

        $this->path = empty( $paths )
            ? null
            : implode( '/', $paths );

        if ( isset( $string ) ) {
            $this->string = trim( $string, '/' );
            $metadata = parse_url( $string );
            $metadata[ 'path' ] = empty( $metadata[ 'path' ] )
                ? null
                : $metadata[ 'path' ];

            $this->scheme = empty( $metadata[ 'scheme' ] )
                ? $this->scheme
                : $metadata[ 'scheme' ];

            if ( $metadata[ 'path' ] === $this->string ) {
                $paths = explode( '/', $this->string );
                $this->origin = $paths[ 0 ];

                $this->path = implode( '/', array_slice( $paths, 1 ) );
            } elseif ( isset( $metadata[ 'host' ] ) ) {
                $this->path = trim( $metadata[ 'path' ] );
                $this->origin = $metadata[ 'host' ];
            }
        }

        $directories = explode( '/', str_replace( '\\', '/', dirname( $_SERVER[ 'SCRIPT_FILENAME' ] ) ) );
        $paths = explode( '/', $this->path );
        $paths = array_intersect( $paths, $directories );

        $this->path = '/' . trim( implode( '/', $paths ), '/' );

        if ( strpos( $this->origin, 'www' ) !== false ) {
            $this->www = true;
            $this->origin = ltrim( $this->origin, 'www.' );
        }

        if ( preg_match( '/(:)([0-9]+)/', $this->string, $matches ) ) {
            $this->port = $matches[ 2 ];
        }

        if ( filter_var( $this->origin, FILTER_VALIDATE_IP ) !== false ) {
            $tlds = [ $this->origin ];
        } else {
            $tlds = explode( '.', $this->origin );
        }

        if ( count( $tlds ) > 1 ) {
            foreach ( $tlds as $key => $tld ) {
                if ( strlen( $tld ) <= 3 AND $key >= 1 ) {
                    $this->tlds[] = $tld;
                }
            }

            if ( empty( $this->tlds ) ) {
                $this->tlds[] = end( $tlds );
            }

            $this->tld = '.' . implode( '.', $this->tlds );

            $this->subDomains = array_diff( $tlds, $this->tlds );
            $this->subDomains = count( $this->subDomains ) == 0
                ? $this->tlds
                : $this->subDomains;

            $this->parentDomain = end( $this->subDomains );
            array_pop( $this->subDomains );

            $this->parentDomain = implode( '.', array_slice( $this->subDomains, 1 ) )
                . '.'
                . $this->parentDomain
                . $this->tld;
            $this->parentDomain = ltrim( $this->parentDomain, '.' );

            if ( count( $this->subDomains ) > 0 ) {
                $this->subDomain = reset( $this->subDomains );
            }
        } else {
            $this->parentDomain = $this->origin;
        }

        $ordinalEnds = [ 'th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th' ];

        foreach ( $this->subDomains as $key => $subdomain ) {
            $ordinalNumber = count( $tlds ) - $key;

            if ( ( ( $ordinalNumber % 100 ) >= 11 ) && ( ( $ordinalNumber % 100 ) <= 13 ) ) {
                $ordinalKey = $ordinalNumber . 'th';
            } else {
                $ordinalKey = $ordinalNumber . $ordinalEnds[ $ordinalNumber % 10 ];
            }

            $this->subDomains[ $ordinalKey ] = $subdomain;

            unset( $this->subDomains[ $key ] );
        }

        foreach ( $this->tlds as $key => $tld ) {
            $ordinalNumber = count( $this->tlds ) - $key;

            if ( ( ( $ordinalNumber % 100 ) >= 11 ) && ( ( $ordinalNumber % 100 ) <= 13 ) ) {
                $ordinalKey = $ordinalNumber . 'th';
            } else {
                $ordinalKey = $ordinalNumber . $ordinalEnds[ $ordinalNumber % 10 ];
            }

            $this->tlds[ $ordinalKey ] = $tld;

            unset( $this->tlds[ $key ] );
        }
    }

    public function getString()
    {
        return $this->string;
    }

    public function getOrigin()
    {
        return $this->origin;
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    public function isWWW()
    {
        return $this->www;
    }

    public function getIpAddress()
    {
        return gethostbyname( $this->origin );
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getParentDomain()
    {
        return $this->parentDomain;
    }

    public function getSubDomain( $level = '3rd' )
    {
        if ( isset( $this->subDomains[ $level ] ) ) {
            return $this->subDomains[ $level ];
        }

        return false;
    }

    public function getSubDomains()
    {
        return $this->subDomains;
    }

    public function getTotalSubDomains()
    {
        return count( $this->subDomains );
    }

    public function getTld( $level = null )
    {
        if ( is_null( $level ) ) {
            return implode( '.', $this->tlds );
        } elseif ( isset( $this->tlds[ $level ] ) ) {
            return $this->tlds[ $level ];
        }

        return false;
    }

    public function getTlds()
    {
        return $this->tlds;
    }

    public function getTotalTlds()
    {
        return count( $this->tlds );
    }
}