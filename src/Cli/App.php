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

use O2System\Kernel\Cli\Writers\Format;

/**
 * Class App
 *
 * Command line interface (cli) application commands container class.
 *
 * @package O2System\Kernel\Cli
 */
class App extends \O2System\Kernel\Cli\App
{
    /**
     * App::optionVersion
     *
     * @return void
     */
    public function optionVersion()
    {
        if (property_exists($this, 'version')) {
            if ( ! empty($this->version)) {
                output()->write(
                    (new Format())
                        ->setString($this->name . ' v' . $this->version . ' Copyright (c) 2011 - ' . date('Y') . ' Steeve Andrian Salim')
                        ->setNewLinesAfter(1)
                );

                output()->write(
                    (new Format())
                        ->setIndent(2)
                        ->setString('this framework is trademark of Steeve Andrian Salim')
                        ->setNewLinesAfter(1)
                );
            }
        }
    }
}