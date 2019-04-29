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
 * Class Csrf
 *
 * @package O2System\Framework\Http\Middleware
 */
class Csrf implements RequestHandlerInterface
{
    /**
     * Csrf::handle
     *
     * Handles a request and produces a response
     *
     * May call other collaborating code to generate the response.
     *
     * @param \O2System\Psr\Http\Message\ServerRequestInterface $request
     */
    public function handle(ServerRequestInterface $request)
    {
        if (services()->has('csrfProtection')) {
            if (hash_equals(input()->server('REQUEST_METHOD'), 'POST')) {
                if ( ! services()->get('csrfProtection')->verify()) {
                    output()->sendError(403, [
                        'message' => language()->getLine('403_INVALID_CSRF'),
                    ]);
                }
            }
        }
    }
}