<?php
/**
 * v6.0.0-svn
 *
 * @author      Steeve Andrian Salim
 * @created     17/11/2016 00:14
 * @copyright   Copyright (c) 2016 Steeve Andrian Salim
 */

if ( ! function_exists( 'base_url' ) ) {
    function base_url ( $uri = null, $suffix = null, $query = [ ] )
    {

    }
}

if ( ! function_exists( 'public_url' ) ) {
    function public_url ( $uri = null, $suffix = null, $query = [ ] )
    {

    }
}

if ( ! function_exists( 'current_url' ) ) {
    function current_url ( $uri = null, $suffix = null, $query = [ ] )
    {

    }
}

if ( ! function_exists( 'assets_url' ) ) {
    function assets_url ( $uri = null, $suffix = null, $query = [ ] )
    {

    }
}

if ( ! function_exists( 'theme_url' ) ) {
    function theme_url ( $uri = null, $suffix = null, $query = [ ] )
    {

    }
}

if ( ! function_exists( 'prepare_url' ) ) {
    /**
     * Prep URL
     *
     * Simply adds the http:// part if no scheme is included
     *
     * @param    string    the URL
     *
     * @return    string
     */
    function prepare_url ( $uri = '' )
    {
        if ( $uri === 'http://' OR $uri === 'https://' OR $uri === '' ) {
            return '';
        }

        $url = parse_url( $uri );

        if ( ! $url OR ! isset( $url[ 'scheme' ] ) ) {
            return ( is_https() ? 'https://' : 'http://' ) . $uri;
        }

        return $uri;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists( 'redirect' ) ) {
    /**
     * Header Redirect
     *
     * Header redirect in two flavors
     * For very fine grained control over headers, you could use the Output
     * Library's set_header() function.
     *
     * @param    string $uri    URL
     * @param    string $method Redirect method
     *                          'auto', 'location' or 'refresh'
     * @param    int    $code   HTTP Response status code
     *
     * @return    void
     */
    function redirect ( $uri = '', $method = 'auto', $code = null )
    {
        if ( substr_count( $uri, '.' ) > 1 ) {
            $segments = explode( '/', $uri );
            $domain = reset( $segments );

            array_shift( $segments );

            $uri = isset( $_SERVER[ 'REQUEST_SCHEME' ] ) ? $_SERVER[ 'REQUEST_SCHEME' ] : 'http';
            $uri .= '://' . $domain;

            // Add server port if needed
            $uri .= $_SERVER[ 'SERVER_PORT' ] !== '80' ? ':' . $_SERVER[ 'SERVER_PORT' ] : '';

            $uri .= empty( $segments ) ? '' : '/' . implode( '/', $segments );
        } else {
            if ( ! preg_match( '#^(\w+:)?//#i', $uri ) ) {
                $uri = base_url( $uri );
            }
        }

        // IIS environment likely? Use 'refresh' for better compatibility
        if ( $method === 'auto' && isset( $_SERVER[ 'SERVER_SOFTWARE' ] ) && strpos(
                                                                                 $_SERVER[ 'SERVER_SOFTWARE' ],
                                                                                 'Microsoft-IIS'
                                                                             ) !== false
        ) {
            $method = 'refresh';
        } elseif ( $method !== 'refresh' && ( empty( $code ) OR ! is_numeric( $code ) ) ) {
            if ( isset( $_SERVER[ 'SERVER_PROTOCOL' ], $_SERVER[ 'REQUEST_METHOD' ] ) && $_SERVER[ 'SERVER_PROTOCOL' ] === 'HTTP/1.1' ) {
                $code = ( $_SERVER[ 'REQUEST_METHOD' ] !== 'GET' )
                    ? 303    // reference: http://en.wikipedia.org/wiki/Post/Redirect/Get
                    : 307;
            } else {
                $code = 302;
            }
        }

        $uri = str_replace( [ 'http://http://', 'https://https://' ], [ 'http://', 'https://' ], $uri );

        switch ( $method ) {
            case 'refresh':
                header( 'Refresh:0;url=' . $uri );
                break;
            default:
                header( 'Location: ' . $uri, true, $code );
                break;
        }

        exit;
    }
}