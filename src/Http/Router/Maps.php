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

use O2System\Framework\Http\Router\Datastructures\Route;
use O2System\Psr\Patterns\AbstractCollectorPattern;

/**
 * Class Collections
 *
 * @package O2System\Framework\Http\Router
 */
class Maps extends AbstractCollectorPattern
{
    public function hasItem( $offset )
    {
        $offset = '/' . trim( $offset, '/' );

        return parent::hasItem( $offset );
    }

    /**
     * getItem
     *
     * @param $offset
     *
     * @return Route
     */
    public function &getItem( $offset )
    {
        $offset = '/' . trim( $offset, '/' );

        return parent::getItem( $offset );
    }

    public function get( $path, \Closure $closure )
    {
        $this->addItem( $path, new Datastructures\Route( 'GET', $path, $closure ) );
    }

    public function addItem( $offset, $item )
    {
        $offset = '/' . trim( $offset, '/' );

        parent::addItem( $offset, $item );
    }

    public function post( $path, \Closure $closure )
    {
        $this->addItem( $path, new Datastructures\Route( 'POST', $path, $closure ) );
    }

    public function head( $path, \Closure $closure )
    {
        $this->addItem( $path, new Datastructures\Route( 'HEAD', $path, $closure ) );
    }

    public function put( $path, \Closure $closure )
    {
        $this->addItem( $path, new Datastructures\Route( 'PUT', $path, $closure ) );
    }

    public function delete( $path, \Closure $closure )
    {
        $this->addItem( $path, new Datastructures\Route( 'DELETE', $path, $closure ) );
    }

    public function options( $path, \Closure $closure )
    {
        $this->addItem( $path, new Datastructures\Route( 'OPTIONS', $path, $closure ) );
    }

    public function trace( $path, \Closure $closure )
    {
        $this->addItem( $path, new Datastructures\Route( 'TRACE', $path, $closure ) );
    }

    public function connect( $path, \Closure $closure )
    {
        $this->addItem( $path, new Datastructures\Route( 'CONNECT', $path, $closure ) );
    }
}