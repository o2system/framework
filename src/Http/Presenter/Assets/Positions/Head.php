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
 * Class Head
 *
 * @package O2System\Framework\Http\Presenter\Assets
 */
class Head extends AbstractPosition
{
    protected $font;
    protected $css;
    protected $javascript;

    public function __construct()
    {
        $this->font = new Collections\Font();
        $this->css = new Collections\Css();
        $this->javascript = new Collections\Javascript();
    }

    public function __toString()
    {
        $output = [];

        // Render fonts
        if ( $this->font->count() ) {
            foreach ( $this->font as $font ) {
                $output[] = '<link rel="stylesheet" type="text/css" href="' . $this->getUrl( $font ) . '">';
            }
        }

        // Render css
        if ( $this->css->count() ) {
            foreach ( $this->css as $css ) {
                $output[] = '<link rel="stylesheet" type="text/css" media="all" href="' . $this->getUrl( $css ) . '">';
            }
        }

        // Render js
        if ( $this->javascript->count() ) {
            foreach ( $this->javascript as $javascript ) {
                $output[] = '<script type="text/javascript" src="' . $this->getUrl( $javascript ) . '"></script>';
            }
        }

        return implode( PHP_EOL, $output );
    }
}