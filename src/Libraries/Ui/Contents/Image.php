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

namespace O2System\Framework\Libraries\Ui\Contents;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Image
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class Image extends Element
{
    /**
     * Image::__construct
     *
     * @param string|null $src
     * @param string|null $alt
     */
    public function __construct($src = null, $alt = null)
    {
        parent::__construct('img');

        if (isset($src)) {
            $this->setSrc($src);
        }

        if (isset($alt)) {
            $this->setAlt($alt);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Image::setSrc
     *
     * @param string $src
     *
     * @return static
     */
    public function setSrc($src)
    {
        if (strpos($src, 'holder.js') !== false) {
            $parts = explode('/', $src);
            $size = end($parts);
            $this->attributes->addAttribute('data-src', $src);

            if ( ! $this->attributes->hasAttribute('alt')) {
                $this->setAlt($size);
            }
        } elseif (strpos($src, 'http')) {
            $this->attributes->addAttribute('src', $src);
        } elseif (is_file($src)) {
            $src = path_to_url($src);
            $this->attributes->addAttribute('src', $src);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Image::setAlt
     *
     * @param string $text
     *
     * @return static
     */
    public function setAlt($text)
    {
        $this->attributes->addAttribute('alt', trim($text));

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Image::setWidth
     *
     * @param int $number
     *
     * @return static
     */
    public function setWidth($number)
    {
        $this->attributes->addAttribute('width', (int)$number);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Image::fluid
     *
     * @return static
     */
    public function fluid()
    {
        $this->attributes->addAttributeClass('img-fluid');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Image::responsive
     *
     * @return static
     */
    public function responsive()
    {
        $this->attributes->addAttributeClass('img-responsive');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Image::setHeight
     *
     * @param int $number
     *
     * @return static
     */
    public function setHeight($number)
    {
        $this->attributes->addAttribute('height', (int)$number);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Image::thumbnailShape
     *
     * @return static
     */
    public function thumbnailShape()
    {
        $this->removeShape();
        $this->attributes->addAttributeClass('img-thumbnail');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Image::removeShape
     *
     * @return static
     */
    protected function removeShape()
    {
        $this->attributes->removeAttributeClass(['img-thumbnail', 'img-rounded', 'img-circle']);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Image::roundedShape
     *
     * @return static
     */
    public function roundedShape()
    {
        $this->removeShape();
        $this->attributes->addAttributeClass('img-rounded');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Image::circleShape
     *
     * @return static
     */
    public function circleShape()
    {
        $this->removeShape();
        $this->attributes->addAttributeClass('img-circle');

        return $this;
    }
}