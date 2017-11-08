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
 * Class Environment
 *
 * @package O2System\Framework\Http\Middleware
 */
class Environment implements MiddlewareServiceInterface
{
    /**
     * validate
     *
     * @param \O2System\Psr\Http\Message\RequestInterface $request
     *
     * @return mixed
     */
    public function validate( RequestInterface $request )
    {
        $clientIpAddress = $request->getClientIpAddress();
        $debugIpAddresses = config( 'ipAddresses' )->offsetGet( 'debug' );

        if ( in_array( $clientIpAddress, $debugIpAddresses ) ) {
            $_ENV[ 'DEBUG_STAGE' ] = 'DEVELOPER';

            return true;
        }

        return false;
    }

    public function handle( RequestInterface $request )
    {
        switch ( strtoupper( $_ENV[ 'DEBUG_STAGE' ] ) ) {
            default:
            case 'DEVELOPER':

                error_reporting( -1 );
                ini_set( 'display_errors', 1 );

                if ( isset( $_REQUEST[ 'PHP_INFO' ] ) ) {
                    phpinfo();
                    exit( EXIT_SUCCESS );
                }

                break;
            case 'TESTER':
            case 'PUBLIC':

                ini_set( 'display_errors', 0 );
                error_reporting( E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED );

                break;
        }
    }

    public function terminate( RequestInterface $request )
    {
        // Nothing to-be terminated
    }
}