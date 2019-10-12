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

namespace O2System\Framework\Cli\Commanders;

// ------------------------------------------------------------------------

use O2System\Framework\Cli\Commander;
use O2System\Kernel\Cli\Writers\Format;
use O2System\Kernel\Cli\Writers\Table;

/**
 * Class Registry
 *
 * @package O2System\Framework\Cli\Commanders
 */
class Registry extends Commander
{
    /**
     * Make::$commandVersion
     *
     * Command version.
     *
     * @var string
     */
    protected $commandVersion = '1.0.0';

    /**
     * Make::$commandDescription
     *
     * Command description.
     *
     * @var string
     */
    protected $commandDescription = 'CLI_REGISTRY_DESC';

    /**
     * Make::$commandOptions
     *
     * Command options.
     *
     * @var array
     */
    protected $commandOptions = [
        'update'   => [
            'description' => 'CLI_REGISTRY_UPDATE_DESC',
            'help'        => 'CLI_REGISTRY_UPDATE_HELP',
        ],
        'flush'    => [
            'description' => 'CLI_REGISTRY_FLUSH_DESC',
        ],
        'info'     => [
            'description' => 'CLI_REGISTRY_INFO_DESC',
        ],
        'metadata' => [
            'description' => 'CLI_REGISTRY_METADATA_DESC',
            'help'        => 'CLI_REGISTRY_METADATA_HELP',
        ],
    ];

    /**
     * Registry::$optionModules
     *
     * @var bool
     */
    protected $optionModules = false;

    /**
     * Registry::$optionLanguages
     *
     * @var bool
     */
    protected $optionLanguages = false;

    // ------------------------------------------------------------------------

    /**
     * Registry::optionModules
     */
    public function optionModules()
    {
        $this->optionModules = true;
    }

    // ------------------------------------------------------------------------

    /**
     * Registry::optionLanguages
     */
    public function optionLanguages()
    {
        $this->optionLanguages = true;
    }

    // ------------------------------------------------------------------------

    /**
     * Registry::update
     *
     * @throws \Exception
     */
    public function update()
    {
        if($this->optionModules) {
            modules()->updateRegistry();
        } elseif($this->optionLanguages) {
            language()->updateRegistry();
        } else {
            modules()->updateRegistry();
            language()->updateRegistry();
        }

        exit(EXIT_SUCCESS);
    }

    // ------------------------------------------------------------------------

    /**
     * Registry::flush
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function flush()
    {
        if($this->optionModules) {
            modules()->flushRegistry();
        } elseif($this->optionLanguages) {
            language()->flushRegistry();
        } else {
            modules()->flushRegistry();
            language()->flushRegistry();
        }

        exit(EXIT_SUCCESS);
    }

    // ------------------------------------------------------------------------

    /**
     * Registry::info
     */
    public function info()
    {
        $table = new Table();

        $table
            ->addHeader('Metadata')
            ->addHeader('Total');

        $table
            ->addRow()
            ->addColumn('Modules')
            ->addColumn(modules()->getTotalRegistry());

        $table
            ->addRow()
            ->addColumn('Language')
            ->addColumn(language()->getTotalRegistry());

        output()->write(
            (new Format())
                ->setString($table->render())
                ->setNewLinesBefore(1)
                ->setNewLinesAfter(2)
        );

        exit(EXIT_SUCCESS);
    }

    // ------------------------------------------------------------------------

    /**
     * Registry::metadata
     */
    public function metadata()
    {
        if($this->optionModules) {
            $line = PHP_EOL . print_r(modules()->getRegistry(), true);
        } elseif($this->optionLanguages) {
            $line = PHP_EOL . print_r(language()->getRegistry(), true);
        } else {
            $line = PHP_EOL . print_r(modules()->getRegistry(), true);
            $line.= PHP_EOL . print_r(language()->getRegistry(), true);
        }

        if (isset($line)) {
            output()->write($line);

            exit(EXIT_SUCCESS);
        }
    }
}