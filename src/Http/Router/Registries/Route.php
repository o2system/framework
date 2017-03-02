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

namespace O2System\Framework\Http\Router\Registries;

// ------------------------------------------------------------------------

/**
 * Class Route
 *
 * @package O2System\Router\Registries
 */
class Route
{
    /**
     * Route Methods
     *
     * @var array
     */
    private $methods;

    /**
     * Route::$domain
     *
     * Routing map domain.
     *
     * @var string
     */
    private $domain;

    /**
     * Route Path
     *
     * @var string
     */
    private $path;

    /**
     * Route Closure
     *
     * @var \Closure
     */
    private $closure;

    /**
     * Route Closure Parameters
     *
     * @var array
     */
    private $closureParameters = [];

    // ------------------------------------------------------------------------

    /**
     * Route::__construct
     *
     * @param string   $method  The route method.
     * @param string   $path    The route path.
     * @param \Closure $closure The route closure.
     * @param string   $domain  The route domain.
     */
    public function __construct ( $method, $path, \Closure $closure, $domain = null )
    {
        $this->methods = explode( '|', $method );
        $this->methods = array_map( 'strtoupper', $this->methods );

        $this->path = $path;
        $this->closure = $closure;
        $this->domain = is_null( $domain )
            ? isset( $_SERVER[ 'HTTP_HOST' ] )
                ? @$_SERVER[ 'HTTP_HOST' ]
                : @$_SERVER[ 'SERVER_NAME' ]
            : $domain;

        if ( preg_match_all( "/{(.*)}/", $this->domain, $matches ) ) {
            foreach ( $matches[ 1 ] as $match ) {
                $this->closureParameters[] = $match;
            }
        }
    }

    // ------------------------------------------------------------------------

    public function getMethods ()
    {
        return $this->methods;
    }

    // ------------------------------------------------------------------------

    public function getDomain ()
    {
        return $this->domain;
    }

    // ------------------------------------------------------------------------

    public function getPath ()
    {
        return $this->path;
    }

    // ------------------------------------------------------------------------

    public function getClosure ()
    {
        return call_user_func_array( $this->closure, $this->closureParameters );
    }

    // ------------------------------------------------------------------------

    public function setClosureParameters ( array $parameters )
    {
        $this->closureParameters = $parameters;

        return $this;
    }

    public function addClosureParameters ( $value )
    {
        $this->closureParameters[] = $value;

        return $this;
    }

    public function getClosureParameters ()
    {
        return $this->closureParameters;
    }

    // ------------------------------------------------------------------------

    public function isValidDomain ()
    {
        $domain = isset( $_SERVER[ 'HTTP_HOST' ] )
            ? $_SERVER[ 'HTTP_HOST' ]
            : $_SERVER[ 'SERVER_NAME' ];

        if ( $this->domain === $domain ) {
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    public function isValidUriString ( $uriString )
    {
        if ( strtolower( $uriString ) === $this->path ) {
            $this->closureParameters = array_merge(
                $this->closureParameters,
                array_filter( explode( '/', $uriString ) )
            );

            return true;
        } elseif ( false !== ( $matches = $this->getParseUriString( $uriString ) ) ) {
            $this->closureParameters = array_merge( $this->closureParameters, $matches );

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    public function getParseUriString ( $string )
    {
        $string = '/' . trim( $string, '/' );
        $regex = str_replace( [ ':any', ':num' ], [ '[^/]+', '[0-9]+' ], $this->path );

        if ( preg_match( '#^' . $regex . '$#', $string, $matches ) ) {
            // Remove the original string from the matches array.
            array_shift( $matches );

            return $matches;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    public function isValidHttpMethod ( $method )
    {
        $method = strtoupper( $method );

        if ( in_array( 'ANY', $this->methods ) ) {
            return true;
        }

        return (bool) in_array( $method, $this->methods );
    }

    // ------------------------------------------------------------------------

    public function isAnyHttpMethod()
    {
        return (bool) in_array( 'ANY', $this->methods );
    }
}