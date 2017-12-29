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
 * Class SignOn
 *
 * @package O2System\Framework\Http\Middleware
 */
class SignOn implements MiddlewareServiceInterface
{
    /**
     * validate
     *
     * @param \O2System\Psr\Http\Message\RequestInterface $request
     *
     * @return mixed
     */
    public function validate(RequestInterface $request)
    {
        if (input()->get('ssid') && o2system()->hasService('user')) {
            return true;
        }

        return false;
    }

    public function handle(RequestInterface $request)
    {
        if (null !== ($ssid = input()->get('ssid'))) {
            if (o2system()->getService('user')->validate($ssid)) {
                set_cookie('ssid', $ssid);

                echo o2system()->getService('user')->getIframeScript();
                exit(EXIT_SUCCESS);
            }
        }
    }

    public function terminate(RequestInterface $request)
    {
        // Nothing to-be terminated
    }
}