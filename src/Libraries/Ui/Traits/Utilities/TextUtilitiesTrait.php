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
 * Trait TypographySetterTrait
 *
 * @package O2System\Framework\Libraries\Ui\Traits\Utilities
 */
trait TextUtilitiesTrait
{
    public function textHide()
    {
        $this->attributes->addAttributeClass('text-hide');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function textLeft()
    {
        $this->attributes->addAttributeClass('text-left');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function textRight()
    {
        $this->attributes->addAttributeClass('text-right');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function textJustify()
    {
        $this->attributes->addAttributeClass('text-justify');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function textCenter()
    {
        $this->attributes->addAttributeClass('text-center');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function textNowrap()
    {
        $this->attributes->addAttributeClass('text-nowrap');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function textTruncate()
    {
        $this->attributes->addAttributeClass('text-truncate');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function textLowercase()
    {
        $this->attributes->addAttributeClass('text-lowercase');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function textUppercase()
    {
        $this->attributes->addAttributeClass('text-uppercase');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function textCapitalize()
    {
        $this->attributes->addAttributeClass('text-capitalize');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function textFontBold()
    {
        $this->attributes->addAttributeClass('font-weight-bold');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function textFontNormal()
    {
        $this->attributes->addAttributeClass('font-weight-normal');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function textFontLight()
    {
        $this->attributes->addAttributeClass('font-weight-light');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function textFontItalic()
    {
        $this->attributes->addAttributeClass('font-italic');

        return $this;
    }
}