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
 * Class SignOn
 *
 * @package O2System\Framework\Http\Middleware
 */
class SignOn implements RequestHandlerInterface
{
    /**
     * Environment::handle
     *
     * Handles a request and produces a response
     *
     * May call other collaborating code to generate the response.
     *
     * @param \O2System\Psr\Http\Message\ServerRequestInterface $request
     */
    public function handle(ServerRequestInterface $request)
    {
        if (null !== ($ssid = input()->get('ssid')) && services()->has('user')) {
            if (services()->get('user')->validate($ssid)) {
                set_cookie('ssid', $ssid);

                echo services()->get('user')->getIframeScript();
                exit(EXIT_SUCCESS);
            }
        }
    }
}