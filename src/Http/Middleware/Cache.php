<?php
/**
 * This file is part of the O2System Framework package.
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

use O2System\Psr\Http\Message\ServerRequestInterface;
use O2System\Psr\Http\Server\RequestHandlerInterface;

/**
 * Class Cache
 *
 * @package O2System\Framework\Http\Middleware
 */
class Cache implements RequestHandlerInterface
{
    /**
     * Cache::handle
     *
     * Handles a request and produces a response
     *
     * May call other collaborating code to generate the response.
     *
     * @param \O2System\Psr\Http\Message\ServerRequestInterface $request
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function handle(ServerRequestInterface $request)
    {
        // Try to get from cache
        $cacheKey = 'o2output_' . underscore(server_request()->getUri()->segments->__toString());

        $cacheHandle = cache()->getItemPool('default');

        if (cache()->hasItemPool('output')) {
            $cacheHandle = cache()->getItemPool('output');
        }

        if ($cacheHandle instanceof \Psr\Cache\CacheItemPoolInterface) {
            if ($cacheHandle->hasItem($cacheKey)) {
                output()
                    ->setContentType('text/html')
                    ->send($cacheHandle->getItem($cacheKey)->get());
            }
        }
    }
}