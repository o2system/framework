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
 * Class Maintenance
 *
 * @package O2System\Framework\Http\Middleware
 */
class Maintenance implements RequestHandlerInterface
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
        if (cache()->hasItem('maintenance')) {
            $maintenanceInfo = cache()->getItem('maintenance')->get();
            echo view()->load('maintenance', $maintenanceInfo, true);
            exit(EXIT_SUCCESS);
        }
    }
}