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
use O2System\Psr\Patterns\AbstractRegistryPattern;

/**
 * Class Middleware
 *
 * @package O2System
 */
class Middleware extends AbstractRegistryPattern
{
    public function __construct ()
    {
        $this->register( 'environment', new Middleware\Environment() );
        $this->register( 'cache', new Middleware\Cache() );
    }

    /**
     * Middleware::run
     *
     * @return void
     */
    public function run ()
    {
        if ( $this->isEmpty() === false ) {
            
            $request = router()->getRequest();

            foreach ( $this->getArrayCopy() as $offset => $service ) {
                if ( $service instanceof MiddlewareServiceInterface ) {
                    if ( $service->validate( $request ) ) {
                        $service->handle( $request );
                    } else {
                        $service->terminate( $request );
                    }
                }

                $this->unregister( $offset );
            }
        }
    }

    /**
     * Middleware::isValid
     *
     * @param mixed $service
     *
     * @return bool
     */
    protected function isValid ( $service )
    {
        if ( $service instanceof MiddlewareServiceInterface ) {
            return true;
        }

        return false;
    }
}