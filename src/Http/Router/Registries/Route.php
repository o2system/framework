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
    private $method;

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
    private $closureParameters = [ ];

    // ------------------------------------------------------------------------

    /**
     * Route::__construct
     *
     * @param          $method
     * @param          $path
     * @param \Closure $closure
     */
    public function __construct ( $method, $path, \Closure $closure )
    {
        $this->method = explode( '|', $method );
        $this->method = array_map( 'strtoupper', $this->method );

        $this->path = $path;
        $this->closure = $closure;
    }

    public function getClosure ()
    {
        return call_user_func_array( $this->closure, $this->closureParameters );
    }

    public function getClosureParameters ()
    {
        return $this->closureParameters;
    }

    public function isValidUriString ( $uriString )
    {
        if ( strtolower( $uriString ) === $this->path ) {
            $this->closureParameters = array_filter( explode( '/', $uriString ) );

            return true;
        } elseif ( false !== ( $matches = $this->getParseUriString( $uriString ) ) ) {
            $this->closureParameters = $matches;

            return true;
        }

        return false;
    }

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

    public function isValidHttpMethod ( $method )
    {
        $method = strtoupper( $method );

        return (bool) in_array( $method, $this->method );
    }


}