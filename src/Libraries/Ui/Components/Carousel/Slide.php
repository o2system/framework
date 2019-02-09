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

namespace O2System\Framework\Libraries\Ui\Components\Carousel;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components\Jumbotron;
use O2System\Framework\Libraries\Ui\Contents\Image;
use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Slide
 *
 * @package O2System\Framework\Libraries\Ui\Components\Carousel
 */
class Slide extends Element
{
    /**
     * Slide::__construct
     */
    public function __construct()
    {
        parent::__construct('div', 'slide');
        $this->attributes->addAttributeClass('carousel-item');
    }

    // ------------------------------------------------------------------------

    /**
     * Slide::active
     *
     * @return static
     */
    public function active()
    {
        $this->attributes->addAttributeClass('active');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Slide::createImage
     *
     * @param string      $src
     * @param string|null $alt
     *
     * @return mixed
     */
    public function createImage($src, $alt = null)
    {
        $this->childNodes->push(new Image($src, $alt));

        return $this->childNodes->last();
    }

    // ------------------------------------------------------------------------

    /**
     * Slide::createJumbotron
     *
     * @return Jumbotron
     */
    public function createJumbotron()
    {
        $this->childNodes->push(new Jumbotron());

        return $this->childNodes->last();
    }
}