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

namespace O2System\Framework\Http\Router;

// ------------------------------------------------------------------------

use O2System\Framework\Http\Message\Uri\Domain;

/**
 * Class Collections
 *
 * @package O2System\Framework\Http\Router
 */
class Routes
{
    /**
     * Routes::HTTP_GET
     *
     * The GET method is used to retrieve information from the given server using a given URI.
     * Requests using GET should only retrieve data and should have no other effect on the data.
     *
     * @var string
     */
    const HTTP_GET = 'GET';

    /**
     * Routes::HTTP_HEAD
     *
     * The HEAD method is used to retrieve only the status line and header section information from the given server
     * using a given URI. Requests using HEAD should only retrieve data and should have no other effect on the data.
     *
     * @var string
     */
    const HTTP_HEAD = 'HEAD';

    /**
     * Routes::HTTP_POST
     *
     * The POST method is used to send data to the server.
     *
     * @var string
     */
    const HTTP_POST = 'POST';

    /**
     * Routes::HTTP_PUT
     *
     * The PUT method is used to replaces all current representations of the target resource with the
     * uploaded content.
     *
     * @var string
     */
    const HTTP_PUT = 'PUT';

    /**
     * Routes::HTTP_DELETE
     *
     * Removes all current representations of the target resource given by a URI.
     *
     * @var string
     */
    const HTTP_DELETE = 'DELETE';

    /**
     * Routes::HTTP_CONNECT
     *
     * Establishes a tunnel to the server identified by a given URI.
     *
     * @var string
     */
    const HTTP_CONNECT = 'CONNECT';

    /**
     * Routes::HTTP_OPTIONS
     *
     * Describes the communication options for the target resource.
     *
     * @var string
     */
    const HTTP_OPTIONS = 'OPTIONS';

    /**
     * Routes::HTTP_TRACE
     *
     * Performs a message loop-back test along the path to the target resource.
     *
     * @var string
     */
    const HTTP_TRACE = 'TRACE';

    /**
     * Routes::HTTP_ANY
     *
     * Any http type.
     *
     * @var string
     */
    const HTTP_ANY = 'ANY';

    /**
     * Routes::$subDomains
     *
     * @var array
     */
    protected $domains = [];

    /**
     * Routes::$maps
     *
     * @var array
     */
    protected $maps = [];

    /**
     * Routes::$attributes
     *
     * @var array
     */
    protected $attributes = [];

    // ------------------------------------------------------------------------

    public function getMap( $path, $domain = null )
    {
        $path = '/' . ltrim( $path, '/' );
        $maps = $this->getMaps( $domain );

        if ( isset( $maps[ $path ] ) ) {
            return $maps[ $path ];
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Routes::getMaps
     *
     * Gets array of domain routes maps.
     *
     * @param string $domain The domain url.
     *
     * @return array Returns array of domain routes maps.
     */
    public function getMaps( $domain = null )
    {
        $hostDomain = new Domain();

        if ( is_null( $domain ) ) {
            $domain = $hostDomain->getOrigin();
        }

        $maps = [];

        if ( isset( $this->maps[ $domain ] ) ) {
            $maps = $this->maps[ $domain ];
        } elseif ( $module = modules()->getModule( $hostDomain->getSubDomains() ) ) {
            // remove autoload routes
            config()->remove( 'routes' );

            // autoload module
            modules()->push( $module );

            // get module routes
            $routes = config()->getItem( 'routes' );

            if ( $routes instanceof Routes ) {
                $maps = $routes->getMaps( $hostDomain->getOrigin() );
            } else {
                $routes = new Routes();
                $controllerClassName = $module->getNamespace() . 'Controllers\\' . camelcase( $module->getParameter() );

                if ( class_exists( $controllerClassName ) ) {
                    $routes->any( '/', function () use ( $controllerClassName ) {
                        return new $controllerClassName();
                    } );

                    return $routes->getMaps( $hostDomain->getOrigin() );
                }
            }
        } else {
            $domain = new Domain( $domain );
            if ( array_key_exists( $domain->getString(), $this->maps ) ) {
                $maps = $this->maps[ $domain->getString() ];
            } else {
                foreach ( $this->maps as $domainRoute => $domainMap ) {
                    if ( preg_match( '/[{][a-zA-Z0-9$_]+[}]/', $domainRoute ) ) {
                        $domainRoute = new Domain( $domainRoute );

                        if ( $domain->getParentDomain() === $domainRoute->getParentDomain() AND
                            $domain->getTotalSubDomains() == $domainRoute->getTotalSubDomains()
                        ) {
                            if ( isset( $domainMap[ $domainRoute->getSubDomain() ] ) ) {
                                $maps = $domainMap;
                                $map = $maps[ $domainRoute->getSubDomain() ]->setClosureParameters(
                                    $domain->getSubDomains()
                                );

                                unset( $maps[ $domainRoute->getSubDomain() ] );

                                if ( false !== ( $closureParameters = $map->getClosure() ) ) {

                                    $closureParameters = ! is_array( $closureParameters )
                                        ? [ $closureParameters ]
                                        : $closureParameters;

                                    foreach ( $maps as $map ) {
                                        $map->setClosureParameters( (array)$closureParameters );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $maps;
    }

    // ------------------------------------------------------------------------

    /**
     * Routes::any
     *
     * @param string $path   The URI string path.
     * @param mixed  $map    The routing map of the URI:
     *                       [string]: string of controller name.
     *                       [array]: array of URI segment.
     *                       [\Closure]: the closure map of URI.
     *
     * @return static
     */
    public function any( $path, $map )
    {
        $this->addMap( $path, $map, self::HTTP_ANY );

        return $this;
    }

    // ------------------------------------------------------------------------

    public function addMap( $path, $map, $method = self::HTTP_GET )
    {
        if ( $map instanceof \Closure ) {
            $closure = $map;
        } else {

            if ( is_string( $map ) ) {
                $namespace = isset( $this->attributes[ 'namespace' ] )
                    ? $this->attributes[ 'namespace' ]
                    : null;
                $controllerClassName = trim( $namespace, '\\' ) . '\\' . $map;

                if ( class_exists( $controllerClassName ) ) {
                    $map = $controllerClassName;
                }
            }

            $closure = function () use ( $map ) {
                return $map;
            };
        }

        $domain = isset( $this->attributes[ 'domain' ] )
            ? $this->attributes[ 'domain' ]
            : null;

        $prefix = isset( $this->attributes[ 'prefix' ] )
            ? $this->attributes[ 'prefix' ]
            : null;

        if ( ! preg_match( '/[{][a-zA-Z0-9$_]+[}]/', $path ) ) {
            $path = '/' . trim( trim( $prefix, '/' ) . '/' . trim( $path, '/' ), '/' );
        }

        $route = new Datastructures\Route( $method, $path, $closure, $domain );

        $this->maps[ $route->getDomain() ][ $route->getPath() ] = $route;

        return $this;
    }

    // ------------------------------------------------------------------------

    public function middleware( array $middleware, $register = true )
    {
        foreach ( $middleware as $offset => $object ) {
            $offset = is_numeric( $offset ) ? $object : $offset;

            if( $register ) {
                middleware()->register( $object, $offset );
            } else {
                middleware()->unregister( $offset );
            }
        }

        return $this;
    }

    public function group( $attributes, \Closure $closure )
    {
        $parentAttributes = $this->attributes;
        $this->attributes = $attributes;

        call_user_func( $closure, $this );

        $this->attributes = $parentAttributes;
    }

    public function domains( array $domains )
    {
        foreach ( $domains as $domain => $map ) {
            $this->domain( $domain, $map );
        }
    }

    public function domain( $domain, $map )
    {
        if ( $domain !== '*' ) {
            $hostDomain = new Domain();
            $domain = str_replace( '.' . $hostDomain->getParentDomain(), '',
                    $domain ) . '.' . $hostDomain->getParentDomain();
        }

        $this->domains[ $domain ] = $map;
    }

    public function getDomain( $domain = null )
    {
        $domain = is_null( $domain )
            ? isset( $_SERVER[ 'HTTP_HOST' ] )
                ? $_SERVER[ 'HTTP_HOST' ]
                : $_SERVER[ 'SERVER_NAME' ]
            : $domain;

        if ( array_key_exists( $domain, $this->domains ) ) {
            if ( is_callable( $this->domains[ $domain ] ) ) {
                return call_user_func( $this->domains[ $domain ] );
            }

            return $this->domains[ $domain ];
        } elseif ( count( $this->domains ) ) {

            // check wildcard domain closure
            if ( isset( $this->domains[ '*' ] ) and is_callable( $this->domains[ '*' ] ) ) {
                if ( false !== ( $map = call_user_func( $this->domains[ '*' ], $domain ) ) ) {
                    return $map;
                }
            }

            // check pregmatch domain closure
            foreach ( $this->domains as $map => $closure ) {
                if ( $map === '*' ) {
                    continue;
                } elseif ( preg_match( '/[{][a-zA-Z0-9$_]+[}]/', $map ) and $closure instanceof \Closure ) {
                    $mapDomain = new Domain( $map );
                    $checkDomain = new Domain( $domain );
                    $parameters = [];

                    if ( $mapDomain->getTotalSubDomains() === $checkDomain->getTotalSubDomains() ) {
                        foreach ( $mapDomain->getSubDomains() as $level => $name ) {
                            if ( false !== ( $checkDomainName = $checkDomain->getSubDomain( $level ) ) ) {
                                $parameters[] = $checkDomainName;
                            }
                        }

                        if ( false !== ( $map = call_user_func_array( $closure, $parameters ) ) ) {
                            return $map;
                            break;
                        }
                    }
                }
            }
        }

        return null;
    }

    // ------------------------------------------------------------------------

    /**
     * Routes::get
     *
     * @param string $path   The URI string path.
     * @param mixed  $map    The routing map of the URI:
     *                       [string]: string of controller name.
     *                       [array]: array of URI segment.
     *                       [\Closure]: the closure map of URI.
     *
     * @return static
     */
    public function get( $path, $map )
    {
        $this->addMap( $path, $map, self::HTTP_GET );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Routes::post
     *
     * @param string $path   The URI string path.
     * @param mixed  $map    The routing map of the URI:
     *                       [string]: string of controller name.
     *                       [array]: array of URI segment.
     *                       [\Closure]: the closure map of URI.
     *
     * @return static
     */
    public function post( $path, $map )
    {
        $this->addMap( $path, $map, self::HTTP_POST );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Routes::put
     *
     * @param string $path   The URI string path.
     * @param mixed  $map    The routing map of the URI:
     *                       [string]: string of controller name.
     *                       [array]: array of URI segment.
     *                       [\Closure]: the closure map of URI.
     *
     * @return static
     */
    public function put( $path, $map )
    {
        $this->addMap( $path, $map, self::HTTP_PUT );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Routes::connect
     *
     * @param string $path   The URI string path.
     * @param mixed  $map    The routing map of the URI:
     *                       [string]: string of controller name.
     *                       [array]: array of URI segment.
     *                       [\Closure]: the closure map of URI.
     *
     * @return static
     */
    public function connect( $path, $map )
    {
        $this->addMap( $path, $map, self::HTTP_CONNECT );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Routes::delete
     *
     * @param string $path   The URI string path.
     * @param mixed  $map    The routing map of the URI:
     *                       [string]: string of controller name.
     *                       [array]: array of URI segment.
     *                       [\Closure]: the closure map of URI.
     *
     * @return static
     */
    public function delete( $path, $map )
    {
        $this->addMap( $path, $map, self::HTTP_DELETE );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Routes::delete
     *
     * @param string $path   The URI string path.
     * @param mixed  $map    The routing map of the URI:
     *                       [string]: string of controller name.
     *                       [array]: array of URI segment.
     *                       [\Closure]: the closure map of URI.
     *
     * @return static
     */
    public function head( $path, $map )
    {
        $this->addMap( $path, $map, self::HTTP_HEAD );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Routes::options
     *
     * @param string $path   The URI string path.
     * @param mixed  $map    The routing map of the URI:
     *                       [string]: string of controller name.
     *                       [array]: array of URI segment.
     *                       [\Closure]: the closure map of URI.
     *
     * @return static
     */
    public function options( $path, $map )
    {
        $this->addMap( $path, $map, self::HTTP_OPTIONS );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Routes::trace
     *
     * @param string $path   The URI string path.
     * @param mixed  $map    The routing map of the URI:
     *                       [string]: string of controller name.
     *                       [array]: array of URI segment.
     *                       [\Closure]: the closure map of URI.
     *
     * @return static
     */
    public function trace( $path, $map )
    {
        $this->addMap( $path, $map, self::HTTP_TRACE );

        return $this;
    }
}