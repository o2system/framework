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

namespace O2System\Framework\Services;

// ------------------------------------------------------------------------

use O2System\Spl\Containers\SplClosureContainer;

/**
 * Class Hooks
 *
 * @package O2System\Framework
 */
class Hooks extends SplClosureContainer
{
    /**
     * Hooks::PRE_SYSTEM
     *
     * @var string
     */
    const PRE_SYSTEM = 'PRE_SYSTEM';

    /**
     * Hooks::POST_SYSTEM
     *
     * @var string
     */
    const POST_SYSTEM = 'POST_SYSTEM';

    /**
     * Hooks::PRE_CONTROLLER
     *
     * @var string
     */
    const PRE_CONTROLLER = 'PRE_CONTROLLER';

    /**
     * Hooks::PRE_COMMANDER
     *
     * @var string
     */
    const PRE_COMMANDER = 'PRE_COMMANDER';

    /**
     * Hooks::POST_CONTROLLER
     *
     * @var string
     */
    const POST_CONTROLLER = 'POST_CONTROLLER';

    /**
     * Hooks::POST_COMMANDER
     *
     * @var string
     */
    const POST_COMMANDER = 'POST_COMMANDER';

    // ------------------------------------------------------------------------

    /**
     * Hooks::__construct
     */
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
                        $this->attach($event, $closure);
                    }
                } elseif ($closures instanceof \Closure) {
                    $this->attach($event, $closures);
                }
            }

            unset($hooks);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * SplClosureContainer::attach
     *
     * Adds an closure object in the registry.
     *
     * @param string   $offset
     * @param \Closure $closure
     */
    public function attach($offset, \Closure $closure)
    {
        if (in_array(strtoupper($offset), [
            self::PRE_SYSTEM,
            self::POST_SYSTEM,
            self::PRE_CONTROLLER,
            self::PRE_COMMANDER,
            self::POST_CONTROLLER,
            self::POST_COMMANDER,
        ])) {
            $this->closures[ $offset ][ spl_object_hash($closure) ] = $closure;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Hooks::callEvent
     *
     * @param string $event
     */
    public function callEvent($event)
    {
        if (array_key_exists($event, $this->closures)) {
            foreach ($this->closures[ $event ] as $closure) {
                call_user_func($closure);
            }
        }
    }
}