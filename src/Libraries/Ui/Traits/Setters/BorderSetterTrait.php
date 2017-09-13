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
 * Trait BorderSetterTrait
 *
 * @package O2System\Framework\Libraries\Ui\Traits\Setters
 */
trait BorderSetterTrait
{
    public function removeBorder()
    {
        $this->attributes->addAttributeClass( 'border-0' );

        return $this;
    }

    public function removeBorderTop()
    {
        $this->attributes->addAttributeClass( 'border-top-0' );

        return $this;
    }

    public function removeBorderBottom()
    {
        $this->attributes->addAttributeClass( 'border-bottom-0' );

        return $this;
    }

    public function removeBorderLeft()
    {
        $this->attributes->addAttributeClass( 'border-left-0' );

        return $this;
    }

    public function removeBorderRight()
    {
        $this->attributes->addAttributeClass( 'border-right-0' );
    }

    public function rounded()
    {
        $this->attributes->removeAttributeClass( [ 'rounded', 'rounded-*' ] );
        $this->attributes->addAttributeClass( 'rounded' );

        return $this;
    }

    public function roundedTop()
    {
        $this->attributes->removeAttributeClass( [ 'rounded', 'rounded-*' ] );
        $this->attributes->addAttributeClass( 'rounded-top' );

        return $this;
    }

    public function roundedBottom()
    {
        $this->attributes->removeAttributeClass( [ 'rounded', 'rounded-*' ] );
        $this->attributes->addAttributeClass( 'rounded-bottom' );

        return $this;
    }

    public function roundedLeft()
    {
        $this->attributes->removeAttributeClass( [ 'rounded', 'rounded-*' ] );
        $this->attributes->addAttributeClass( 'rounded-left' );

        return $this;
    }

    public function roundedRight()
    {
        $this->attributes->removeAttributeClass( [ 'rounded', 'rounded-*' ] );
        $this->attributes->addAttributeClass( 'rounded-right' );

        return $this;
    }

    public function circle()
    {
        $this->attributes->removeAttributeClass( [ 'rounded', 'rounded-*' ] );
        $this->attributes->addAttributeClass( 'rounded-circle' );

        return $this;
    }

    public function square()
    {
        $this->attributes->removeAttributeClass( [ 'rounded', 'rounded-*' ] );
        $this->attributes->addAttributeClass( 'rounded-0' );

        return $this;
    }
}