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
 * Trait BorderSetterTrait
 *
 * @package O2System\Framework\Libraries\Ui\Traits\Utilities
 */
trait BorderUtilitiesTrait
{
    public function removeBorder()
    {
        $this->attributes->addAttributeClass('border-0');

        return $this;
    }

    public function removeBorderTop()
    {
        $this->attributes->addAttributeClass('border-top-0');

        return $this;
    }

    public function removeBorderBottom()
    {
        $this->attributes->addAttributeClass('border-bottom-0');

        return $this;
    }

    public function removeBorderLeft()
    {
        $this->attributes->addAttributeClass('border-left-0');

        return $this;
    }

    public function removeBorderRight()
    {
        $this->attributes->addAttributeClass('border-right-0');
    }

    public function contextBorderDefault()
    {
        $this->attributes->addAttributeClass('border-default');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function contextBorderPrimary()
    {
        $this->attributes->addAttributeClass('border-primary');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function contextBorderSecondary()
    {
        $this->attributes->addAttributeClass('border-secondary');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function contextBorderSuccess()
    {
        $this->attributes->addAttributeClass('border-success');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function contextBorderInfo()
    {
        $this->attributes->addAttributeClass('border-info');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function contextBorderWarning()
    {
        $this->attributes->addAttributeClass('border-warning');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function contextBorderDanger()
    {
        $this->attributes->addAttributeClass('border-danger');

        return $this;
    }

    public function contextBorderLight()
    {
        $this->attributes->addAttributeClass('border-light');

        return $this;
    }

    public function contextBorderDark()
    {
        $this->attributes->addAttributeClass('border-dark');

        return $this;
    }
}