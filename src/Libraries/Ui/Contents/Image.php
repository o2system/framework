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

    public function setAlt($text)
    {
        $this->attributes->addAttribute('alt', trim($text));

        return $this;
    }

    public function setWidth($number)
    {
        $this->attributes->addAttribute('width', (int)$number);

        return $this;
    }

    public function fluid()
    {
        $this->attributes->addAttributeClass('img-fluid');

        return $this;
    }

    public function responsive()
    {
        $this->attributes->addAttributeClass('img-responsive');

        return $this;
    }

    public function setHeight($number)
    {
        $this->attributes->addAttribute('height', (int)$number);

        return $this;
    }

    public function thumbnailShape()
    {
        $this->removeShape();
        $this->attributes->addAttributeClass('img-thumbnail');

        return $this;
    }

    protected function removeShape()
    {
        $this->attributes->removeAttributeClass(['img-thumbnail', 'img-rounded', 'img-circle']);
    }

    public function roundedShape()
    {
        $this->removeShape();
        $this->attributes->addAttributeClass('img-rounded');

        return $this;
    }

    public function circleShape()
    {
        $this->removeShape();
        $this->attributes->addAttributeClass('img-circle');

        return $this;
    }
}