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

namespace O2System\Framework\Http\Middleware;

// ------------------------------------------------------------------------

use O2System\Psr\Http\Message\RequestInterface;
use O2System\Psr\Http\Middleware\MiddlewareServiceInterface;

/**
 * Class Cache
 *
 * @package O2System\Framework\Http\Middleware
 */
class Cache implements MiddlewareServiceInterface
{
    /**
     * Cache::$cacheKey
     *
     * @var string
     */
    protected $cacheKey;

    /**
     * Cache::$cacheHandle
     *
     * @var \O2System\Psr\Cache\CacheItemPoolInterface
     */
    protected $cacheHandle;

    // ------------------------------------------------------------------------

    /**
     * Cache::validate
     *
     * @param \O2System\Psr\Http\Message\RequestInterface $request
     *
     * @return bool
     */
    public function validate( RequestInterface $request )
    {
        // Try to get from cache
        $this->cacheKey = 'o2output_' . underscore( request()->getUri()->getSegments()->getString() );

        $this->cacheHandle = cache()->getItemPool( 'default' );

        if ( cache()->hasItemPool( 'output' ) ) {
            $this->cacheHandle = cache()->getItemPool( 'output' );
        }

        if ( $this->cacheHandle instanceof \O2System\Psr\Cache\CacheItemPoolInterface ) {
            if ( $this->cacheHandle->hasItem( $this->cacheKey ) ) {
                return true;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Cache::handle
     *
     * @param \O2System\Psr\Http\Message\RequestInterface $request
     *
     * @return void
     */
    public function handle( RequestInterface $request )
    {
        output()
            ->setContentType('text/html')
            ->send( $this->cacheHandle->getItem( $this->cacheKey )->get() );
    }

    // ------------------------------------------------------------------------

    /**
     * Cache::terminate
     *
     * @param \O2System\Psr\Http\Message\RequestInterface $request
     *
     * @return void
     */
    public function terminate( RequestInterface $request )
    {
        // Nothing to be terminated
    }
}