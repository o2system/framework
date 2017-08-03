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
     * validate
     *
     * @param \O2System\Psr\Http\Message\RequestInterface $request
     *
     * @return bool
     */
    public function validate( RequestInterface $request )
    {
        $filename = md5( $request->getUri()->__toString() );

        if ( null !== ( $cache = cache()->getItemPool( 'html' ) ) ) {
            if ( null !== ( $html = $cache->getItem( $filename ) ) ) {
                return true;
            }
        }

        return false;
    }

    public function handle( RequestInterface $request )
    {
        $filename = md5( $request->getUri()->__toString() );
        $cache = cache()->getItemPool( 'html' );

        if ( null !== ( $html = $cache->getItem( $filename ) ) ) {
            $etag = md5( $html );
            output()->send( $html, [ 'ETag' => $etag ] );
        }
    }

    public function terminate( RequestInterface $request )
    {
        // TODO: Implement terminate() method.
    }
}