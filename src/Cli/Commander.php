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

namespace O2System\Framework\Cli;

// ------------------------------------------------------------------------

use O2System\Kernel\Cli\Abstracts\AbstractCommander;
use O2System\Kernel\Cli\Writers\Format;

/**
 * Class Commander
 *
 * @package O2System\Framework\Abstracts
 */
abstract class Commander extends AbstractCommander
{
    /**
     * Commander::$app
     *
     * Commander cli-app.
     *
     * @var App
     */
    protected $app;

    // ------------------------------------------------------------------------

    /**
     * Commander::setApp
     *
     * @param \O2System\Framework\Cli\App $app
     */
    public function setApp( App $app )
    {
        $this->app = $app;
    }

    /**
     * Commander::optionVersion
     *
     * Option version method, write commander version string.
     *
     * @return void
     */
    public function optionVersion()
    {
        if ( property_exists( $this, 'commandVersion' ) ) {
            if ( ! empty( $this->commandVersion ) ) {
                // Show Name & Version Line
                output()->write(
                    ( new Format() )
                        ->setString( $this->optionVersion() . ucfirst( $this->commandName ) . ' v' . $this->commandVersion )
                        ->setNewLinesAfter( 1 )
                );
            }
        }
    }

    // ------------------------------------------------------------------------
}