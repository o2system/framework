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

namespace O2System\Framework\Http;

// ------------------------------------------------------------------------

use O2System\Psr\Http\Message\ServerRequestInterface;
use O2System\Psr\Http\Server\MiddlewareInterface;
use O2System\Psr\Http\Server\RequestHandlerInterface;
use O2System\Spl\Patterns\Structural\Provider\AbstractProvider;
use O2System\Spl\Patterns\Structural\Provider\ValidationInterface;

/**
 * Class Middleware
 *
 * @package O2System
 */
class Middleware extends AbstractProvider implements
    ValidationInterface,
    MiddlewareInterface
{
    /**
     * Middleware::__construct
     */
    public function __construct()
    {
        $this->register(new Middleware\Environment(), 'environment');
        $this->register(new Middleware\Maintenance(), 'maintenance');
        $this->register(new Middleware\Csrf(), 'csrf');
        $this->register(new Middleware\SignOn(), 'sign-on');
        $this->register(new Middleware\Cache(), 'cache');
    }

    // ------------------------------------------------------------------------

    /**
     * Middleware::run
     *
     * @return void
     */
    public function run()
    {
        if ($this->count()) {

            $request = server_request();

            foreach ($this->registry as $offset => $handler) {
                $this->process($request, $handler);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Middleware::process
     * 
     * Process an incoming server request
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     * 
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler)
    {
        $handler->handle($request);
    }

    // ------------------------------------------------------------------------

    /**
     * Middleware::validate
     * 
     * Validate if the handler is an instanceof RequestHandlerInterface.
     *
     * @param mixed $handler
     *
     * @return bool
     */
    public function validate($handler)
    {
        if ($handler instanceof RequestHandlerInterface) {
            return true;
        }

        return false;
    }
}