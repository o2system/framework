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

namespace O2System\Framework\Http\Presenter\Assets\Positions;

// ------------------------------------------------------------------------

use O2System\Framework\Http\Presenter\Assets\Abstracts\AbstractPosition;
use O2System\Framework\Http\Presenter\Assets\Collections;

/**
 * Class Body
 *
 * @package O2System\Framework\Http\Presenter\Assets
 */
class Body extends AbstractPosition
{
    protected $javascript;

    public function __construct()
    {
        $this->javascript = new Collections\Javascript();
    }

    public function __toString()
    {
        $output = [];

        // Render js
        if ( $this->javascript->count() ) {
            foreach ( $this->javascript as $javascript ) {
                $output[] = '<script type="text/javascript" src="' . $this->getUrl( $javascript ) . '" defer="defer"></script>';
            }
        }

        return implode( PHP_EOL, $output );
    }
}