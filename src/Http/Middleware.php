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

namespace O2System\Framework\Http;

// ------------------------------------------------------------------------

use O2System\Psr\Http\Middleware\MiddlewareServiceInterface;
use O2System\Psr\Patterns\Structural\Provider\AbstractProvider;
use O2System\Psr\Patterns\Structural\Provider\ValidationInterface;

/**
 * Class Middleware
 *
 * @package O2System
 */
class Middleware extends AbstractProvider implements ValidationInterface
{
    public function __construct()
    {
        $this->register( new Middleware\Environment(), 'environment' );
        $this->register( new Middleware\Maintenance(), 'maintenance' );
        $this->register( new Middleware\SignOn(), 'sign-on' );
        $this->register( new Middleware\Cache(), 'cache' );
    }

    /**
     * Middleware::run
     *
     * @return void
     */
    public function run()
    {
        if ( $this->count() ) {

            $request = request();

            foreach ( $this->registry as $offset => $service ) {
                if ( $service instanceof MiddlewareServiceInterface ) {
                    if ( $service->validate( $request ) ) {
                        $service->handle( $request );
                    } else {
                        $service->terminate( $request );
                    }
                }

                $this->remove( $offset );
            }
        }
    }

    /**
     * Middleware::validate
     *
     * @param mixed $service
     *
     * @return bool
     */
    public function validate( $service )
    {
        if ( $service instanceof MiddlewareServiceInterface ) {
            return true;
        }

        return false;
    }
}