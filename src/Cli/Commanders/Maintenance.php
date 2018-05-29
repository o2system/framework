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

namespace O2System\Framework\Cli\Commanders;

// ------------------------------------------------------------------------

use O2System\Cache\Item;
use O2System\Kernel\Cli\Commander;
use O2System\Kernel\Cli\Writers\Format;

/**
 * Class Maintenance
 *
 * @package O2System\Framework\Cli\Commanders
 */
class Maintenance extends Commander
{
    /**
     * Maintenance::$commandVersion
     *
     * Command version.
     *
     * @var string
     */
    protected $commandVersion = '1.0.0';

    /**
     * Maintenance::$commandDescription
     *
     * Command description.
     *
     * @var string
     */
    protected $commandDescription = 'CLI_MAINTENANCE_DESC';

    /**
     * Maintenance::$commandOptions
     *
     * Command options.
     *
     * @var array
     */
    protected $commandOptions = [
        'mode'    => [
            'description' => 'CLI_MAINTENANCE_MODE_HELP',
            'required'    => true,
        ],
        'switch'  => [
            'description' => 'CLI_MAINTENANCE_SWITCH_HELP',
            'required'    => true,
        ],
        'title'   => [
            'description' => 'CLI_MAINTENANCE_TITLE_HELP',
            'required'    => false,
        ],
        'message' => [
            'description' => 'CLI_MAINTENANCE_MESSAGE_HELP',
            'required'    => false,
        ],
    ];

    /**
     * Maintenance::$optionSwitch
     *
     * Maintenance switch.
     *
     * @var string
     */
    protected $optionSwitch;

    /**
     * Maintenance::$optionMode
     *
     * Maintenance mode.
     *
     * @var string
     */
    protected $optionMode;

    /**
     * Maintenance::$optionLifetime
     *
     * Maintenance mode.
     *
     * @var string
     */
    protected $optionLifetime;

    /**
     * Maintenance::$optionTitle
     *
     * Maintenance title.
     *
     * @var string
     */
    protected $optionTitle;

    /**
     * Maintenance::$optionMessage
     *
     * Maintenance message.
     *
     * @var string
     */
    protected $optionMessage;

    // ------------------------------------------------------------------------

    public function optionSwitch($switch)
    {
        $switch = strtoupper($switch);

        if (in_array($switch, ['ON', 'OFF'])) {
            $this->optionSwitch = $switch;
        }
    }

    public function optionMode($mode)
    {
        $this->optionMode = $mode;
    }

    public function optionLifetime($lifetime)
    {
        $this->optionLifetime = (int)$lifetime;
    }

    public function optionTitle($title)
    {
        $this->optionTitle = trim($title);
    }

    public function optionMessage($message)
    {
        $this->optionMessage = $message;
    }

    public function execute()
    {
        $options = input()->get();

        if (empty($options)) {
            $_GET[ 'switch' ] = 'ON';
            $_GET[ 'mode' ] = 'default';
            $_GET[ 'lifetime' ] = 300;
            $_GET[ 'title' ] = language()->getLine(strtoupper('CLI_MAINTENANCE_TITLE'));
            $_GET[ 'message' ] = language()->getLine(strtoupper('CLI_MAINTENANCE_MESSAGE'));
        } else {
            $_GET[ 'mode' ] = 'default';
            $_GET[ 'lifetime' ] = 300;
            $_GET[ 'title' ] = language()->getLine(strtoupper('CLI_MAINTENANCE_TITLE'));
            $_GET[ 'message' ] = language()->getLine(strtoupper('CLI_MAINTENANCE_MESSAGE'));
        }

        parent::execute();

        if ($this->optionSwitch === 'ON') {
            if (cache()->hasItem('maintenance')) {

                $maintenanceInfo = cache()->getItem('maintenance')->get();
                output()->write(
                    (new Format())
                        ->setContextualClass(Format::DANGER)
                        ->setString(language()->getLine('CLI_MAINTENANCE_ALREADY_STARTED', [
                            $maintenanceInfo[ 'mode' ],
                            $maintenanceInfo[ 'datetime' ],
                            date('r', strtotime($maintenanceInfo[ 'datetime' ]) + $maintenanceInfo[ 'lifetime' ]),
                            $maintenanceInfo[ 'title' ],
                            $maintenanceInfo[ 'message' ],
                        ]))
                        ->setNewLinesAfter(1)
                );
            } else {
                output()->write(
                    (new Format())
                        ->setContextualClass(Format::WARNING)
                        ->setString(language()->getLine('CLI_MAINTENANCE_STARTED', [
                            $datetime = date('r'),
                            $this->optionLifetime,
                            $this->optionMode,
                            $this->optionTitle,
                            $this->optionMessage,
                        ]))
                        ->setNewLinesAfter(1)
                );

                cache()->save(new Item('maintenance', [
                    'datetime' => $datetime,
                    'lifetime' => $this->optionLifetime,
                    'mode'     => $this->optionMode,
                    'title'    => $this->optionTitle,
                    'message'  => $this->optionMessage,
                ], $this->optionLifetime));
            }

        } elseif ($this->optionSwitch === 'OFF') {
            if (cache()->hasItem('maintenance')) {
                output()->write(
                    (new Format())
                        ->setContextualClass(Format::DANGER)
                        ->setString(language()->getLine('CLI_MAINTENANCE_STOPPED', [
                            $this->optionMode,
                            date('r'),
                        ]))
                        ->setNewLinesAfter(1)
                );

                cache()->deleteItem('maintenance');
            } else {
                output()->write(
                    (new Format())
                        ->setContextualClass(Format::DANGER)
                        ->setString(language()->getLine('CLI_MAINTENANCE_INACTIVE'))
                        ->setNewLinesAfter(1)
                );
            }
        }
    }
}