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

namespace O2System\Framework\Libraries\Ui\Traits\Setters;

// ------------------------------------------------------------------------

/**
 * Trait TypographySetterTrait
 *
 * @package O2System\Framework\Libraries\Ui\Traits\Setters
 */
trait TypographySetterTrait
{
    public function textHide()
    {
        $this->attributes->addAttributeClass( 'text-hide' );
        return $this;
    }

    // ------------------------------------------------------------------------

    public function textLeft()
    {
        $this->attributes->addAttributeClass( 'text-left' );
        return $this;
    }

    // ------------------------------------------------------------------------

    public function textRight()
    {
        $this->attributes->addAttributeClass( 'text-right' );
        return $this;
    }

    // ------------------------------------------------------------------------

    public function textJustify()
    {
        $this->attributes->addAttributeClass( 'text-justify' );
        return $this;
    }

    // ------------------------------------------------------------------------

    public function textCenter()
    {
        $this->attributes->addAttributeClass( 'text-center' );
        return $this;
    }
}