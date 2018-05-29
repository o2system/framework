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

namespace O2System\Framework\Libraries\Ui\Traits\Utilities;

// ------------------------------------------------------------------------

/**
 * Trait SpacingUtilitiesTrait
 * @package O2System\Framework\Libraries\Ui\Traits\Utilities
 */
trait SpacingUtilitiesTrait
{
    public function margin($pixel)
    {
        $this->attributes->addAttributeClass('m-' . (int)$pixel);

        return $this;
    }

    public function marginTop($pixel)
    {
        $this->attributes->addAttributeClass('mt-' . (int)$pixel);

        return $this;
    }

    public function marginBottom($pixel)
    {
        $this->attributes->addAttributeClass('mb-' . (int)$pixel);

        return $this;
    }

    public function marginTopBottom($pixel)
    {
        $this->attributes->addAttributeClass('my-' . (int)$pixel);

        return $this;
    }

    public function marginLeft($pixel)
    {
        $this->attributes->addAttributeClass('ml-' . (int)$pixel);

        return $this;
    }

    public function marginRight($pixel)
    {
        $this->attributes->addAttributeClass('mr-' . (int)$pixel);

        return $this;
    }

    public function marginLeftRight($pixel)
    {
        $this->attributes->addAttributeClass('mx-' . (int)$pixel);

        return $this;
    }

    public function marginAuto()
    {
        $this->attributes->addAttributeClass('mx-auto');

        return $this;
    }

    public function padding($pixel)
    {
        $this->attributes->addAttributeClass('p-' . (int)$pixel);

        return $this;
    }

    public function paddingTop($pixel)
    {
        $this->attributes->addAttributeClass('pt-' . (int)$pixel);

        return $this;
    }

    public function paddingBottom($pixel)
    {
        $this->attributes->addAttributeClass('pb-' . (int)$pixel);

        return $this;
    }

    public function paddingTopBottom($pixel)
    {
        $this->attributes->addAttributeClass('py-' . (int)$pixel);

        return $this;
    }

    public function paddingLeft($pixel)
    {
        $this->attributes->addAttributeClass('pl-' . (int)$pixel);

        return $this;
    }

    public function paddingRight($pixel)
    {
        $this->attributes->addAttributeClass('pr-' . (int)$pixel);

        return $this;
    }

    public function paddingLeftRight($pixel)
    {
        $this->attributes->addAttributeClass('px-' . (int)$pixel);

        return $this;
    }
}