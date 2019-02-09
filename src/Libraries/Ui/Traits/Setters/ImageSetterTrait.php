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

namespace O2System\Framework\Libraries\Ui\Traits\Setters;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;

/**
 * Trait ImageSetterTrait
 *
 * @package O2System\Framework\Libraries\Ui\Traits\Setters
 */
trait ImageSetterTrait
{
    /**
     * ImageSetterTrait::$image
     *
     * @var \O2System\Framework\Libraries\Ui\Contents\Image
     */
    public $image;

    // ------------------------------------------------------------------------

    /**
     * ImageSetterTrait::setImage
     *
     * @param string       $src
     * @param string|null  $alt
     *
     * @return static
     */
    public function setImage($src, $alt = null)
    {
        $this->image = new Element('img');

        if (strpos($src, 'holder.js') !== false) {
            $parts = explode('/', $src);
            $size = end($parts);
            $this->image->attributes->addAttribute('data-src', $src);

            $alt = empty($alt) ? $size : $alt;
        } else {
            $this->image->attributes->addAttribute('src', $src);
        }

        $this->image->attributes->addAttribute('alt', $alt);

        return $this;
    }
}