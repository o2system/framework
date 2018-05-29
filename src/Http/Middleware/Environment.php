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

use O2System\Psr\Http\Message\ServerRequestInterface;
use O2System\Psr\Http\Server\RequestHandlerInterface;

/**
 * Class Environment
 *
 * @package O2System\Framework\Http\Middleware
 */
class Environment implements RequestHandlerInterface
{
    /**
     * Environment::handle
     *
     * Handles a request and produces a response
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $request)
    {
        $clientIpAddress = $request->getClientIpAddress();
        $debugIpAddresses = config('ipAddresses')->offsetGet('debug');

        if (in_array($clientIpAddress, $debugIpAddresses)) {
            $_ENV[ 'DEBUG_STAGE' ] = 'DEVELOPER';

            error_reporting(-1);
            ini_set('display_errors', 1);

            if (isset($_REQUEST[ 'PHP_INFO' ])) {
                phpinfo();
                exit(EXIT_SUCCESS);
            }
        } else {
            ini_set('display_errors', 0);
            error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        }
    }
}