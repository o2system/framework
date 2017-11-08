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
 * Class Maintenance
 *
 * @package O2System\Framework\Http\Middleware
 */
class Maintenance implements MiddlewareServiceInterface
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
        if ( cache()->hasItem( 'maintenance' ) ) {
            return true;
        }

        return false;
    }

    public function handle( RequestInterface $request )
    {
        $maintenanceInfo = cache()->getItem( 'maintenance' )->get();
        echo view()->load( 'maintenance', $maintenanceInfo, true );
        exit( EXIT_SUCCESS );
    }

    public function terminate( RequestInterface $request )
    {
        // Nothing to-be terminated
    }
}