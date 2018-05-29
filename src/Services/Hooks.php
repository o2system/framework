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

namespace O2System\Framework\Services;

// ------------------------------------------------------------------------

/**
 * Class Hooks
 *
 * @package O2System\Framework
 */
class Hooks
{
    const PRE_SYSTEM = 'PRE_SYSTEM';

    const POST_SYSTEM = 'POST_SYSTEM';

    const PRE_CONTROLLER = 'PRE_CONTROLLER';

    const POST_CONTROLLER = 'POST_CONTROLLER';

    /**
     * Hooks Closures
     *
     * @var array
     */
    private $closures = [];

    public function __construct()
    {
        if (is_file(
            $filePath = PATH_APP . 'Config' . DIRECTORY_SEPARATOR . strtolower(
                    ENVIRONMENT
                ) . DIRECTORY_SEPARATOR . 'Hooks.php'
        )) {
            include($filePath);
        } elseif (is_file($filePath = PATH_APP . 'Config' . DIRECTORY_SEPARATOR . 'Hooks.php')) {
            include($filePath);
        }

        if (isset($hooks) AND is_array($hooks)) {
            foreach ($hooks as $event => $closures) {
                if (is_array($closures)) {
                    foreach ($closures as $closure) {
                        $this->addClosure($closure, $event);
                    }
                } elseif ($closures instanceof \Closure) {
                    $this->addClosure($closures, $event);
                }
            }

            unset($hooks);
        }
    }

    public function addClosure(\Closure $closure, $event)
    {
        $event = strtoupper($event);

        if (in_array($event, ['PRE_SYSTEM', 'POST_SYSTEM', 'PRE_CONTROLLER', 'POST_CONTROLLER'])) {
            $this->closures[ $event ][] = $closure;
        }
    }

    public function callEvent($event)
    {
        if (array_key_exists($event, $this->closures)) {
            foreach ($this->closures[ $event ] as $closure) {
                call_user_func($closure);
            }
        }
    }
}