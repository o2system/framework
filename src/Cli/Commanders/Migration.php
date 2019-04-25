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

/**
 * Class Migration
 * @package O2System\Framework\Cli\Commanders
 */
class Migration extends Commander
{
    /**
     * Migration::$commandVersion
     *
     * Command version.
     *
     * @var string
     */
    protected $commandVersion = '1.0.0';

    /**
     * Migration::$commandDescription
     *
     * Command description.
     *
     * @var string
     */
    protected $commandDescription = 'CLI_MIGRATION_DESC';

    /**
     * Migration::$commandOptions
     *
     * Command options.
     *
     * @var array
     */
    protected $commandOptions = [
        'version'  => [
            'description' => 'CLI_MIGRATION_VERSION_HELP',
            'required'    => false,
        ],
        'reset'    => [
            'description' => 'CLI_MIGRATION_RESET_HELP',
            'required'    => true,
        ],
        'rollback' => [
            'description' => 'CLI_MIGRATION_ROLLBACK_HELP',
            'required'    => false,
        ],
        'refresh'  => [
            'description' => 'CLI_MIGRATION_REFRESH_HELP',
            'required'    => false,
        ],
        'fresh'    => [
            'description' => 'CLI_MIGRATION_FRESH_HELP',
            'required'    => false,
        ],
        'seed'     => [
            'description' => 'CLI_MIGRATION_SEED_HELP',
            'required'    => false,
        ],
    ];

    /**
     * Migration::$optionVersion
     *
     * @var string
     */
    protected $optionVersion;

    /**
     * Migration::$optionReset
     *
     * @var bool
     */
    protected $optionReset = false;

    /**
     * Migration::$optionRollback
     *
     * @var bool
     */
    protected $optionRollback = false;

    /**
     * Migration::$optionFresh
     *
     * @var bool
     */
    protected $optionFresh = false;

    /**
     * Migration::$optionSeed
     *
     * @var bool
     */
    protected $optionSeed = false;

    // ------------------------------------------------------------------------

    /**
     * Migration::optionVersion
     *
     * @param string $version
     */
    public function optionVersion($version)
    {
        $this->optionVersion = $version;
    }

    // ------------------------------------------------------------------------

    /**
     * Migration::optionReset
     *
     * @param bool $reset
     */
    public function optionReset($reset)
    {
        $this->optionReset = (bool)$reset;
    }

    // ------------------------------------------------------------------------

    /**
     * Migration::rollback
     *
     * @param bool $rollback
     */
    public function optionRollback($rollback)
    {
        $this->optionRollback = (bool)$rollback;
    }

    // ------------------------------------------------------------------------

    /**
     * Migration::optionFresh
     *
     * @param bool $fresh
     */
    public function optionFresh($fresh)
    {
        $this->optionFresh = (bool)$fresh;
    }

    // ------------------------------------------------------------------------

    /**
     * Migration::execute
     *
     * @throws \ReflectionException
     */
    public function execute()
    {
        $options = input()->get();

        parent::execute();


    }
}