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
 * Trait ColorSetterTrait
 *
 * @package O2System\Framework\Libraries\Ui\Traits\Setters
 */
trait ColorSetterTrait
{
    public function bgPrimary()
    {
        $this->attributes->addAttributeClass( 'bg-primary' );

        return $this;
    }

    public function bgSecondary()
    {
        $this->attributes->addAttributeClass( 'bg-secondary' );

        return $this;
    }

    public function bgSuccess()
    {
        $this->attributes->addAttributeClass( 'bg-success' );

        return $this;
    }

    public function bgInfo()
    {
        $this->attributes->addAttributeClass( 'bg-info' );

        return $this;
    }

    public function bgWarning()
    {
        $this->attributes->addAttributeClass( 'bg-warning' );

        return $this;
    }

    public function bgDanger()
    {
        $this->attributes->addAttributeClass( 'bg-danger' );

        return $this;
    }

    public function bgInverse()
    {
        $this->attributes->addAttributeClass( 'bg-inverse' );

        return $this;
    }

    public function bgFaded()
    {
        $this->attributes->addAttributeClass( 'bg-faded' );

        return $this;
    }

    public function bgTransparent()
    {
        $this->attributes->addAttributeClass( 'bg-transparent' );

        return $this;
    }

    public function bgWhite()
    {
        $this->attributes->addAttributeClass( 'bg-white' );

        return $this;
    }

    public function textWhite()
    {
        $this->attributes->addAttributeClass( 'text-white' );

        return $this;
    }

    public function textMuted()
    {
        $this->attributes->addAttributeClass( 'text-muted' );

        return $this;
    }

    public function textPrimary()
    {
        $this->attributes->addAttributeClass( 'text-primary' );

        return $this;
    }

    public function textSecondary()
    {
        $this->attributes->addAttributeClass( 'text-secondary' );

        return $this;
    }

    public function textSuccess()
    {
        $this->attributes->addAttributeClass( 'text-success' );

        return $this;
    }

    public function textInfo()
    {
        $this->attributes->addAttributeClass( 'text-info' );

        return $this;
    }

    public function textWarning()
    {
        $this->attributes->addAttributeClass( 'text-warning' );

        return $this;
    }

    public function textDanger()
    {
        $this->attributes->addAttributeClass( 'text-danger' );

        return $this;
    }
}