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

namespace O2System\Framework\Libraries\Ui\Traits\Utilities;

/**
 * Trait ColorUtilitiesTrait
 * @package O2System\Framework\Libraries\Ui\Traits\Utilities
 */
trait ColorUtilitiesTrait
{
    public function textPrimary()
    {
        $this->attributes->addAttributeClass('text-primary');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function textSecondary()
    {
        $this->attributes->addAttributeClass('text-secondary');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function textSuccess()
    {
        $this->attributes->addAttributeClass('text-success');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function textDanger()
    {
        $this->attributes->addAttributeClass('text-danger');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function textWarning()
    {
        $this->attributes->addAttributeClass('text-warning');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function textInfo()
    {
        $this->attributes->addAttributeClass('text-info');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function textMuted()
    {
        $this->attributes->addAttributeClass('text-muted');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function textWhite()
    {
        $this->attributes->addAttributeClass('text-white');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function backgroundPrimary($gradient = false)
    {
        $this->attributes->addAttributeClass('bg-' . ($gradient === true ? 'gradient-' : '') . 'primary');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function backgroundSecondary($gradient = false)
    {
        $this->attributes->addAttributeClass('bg-' . ($gradient === true ? 'gradient-' : '') . 'secondary');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function backgroundSuccess($gradient = false)
    {
        $this->attributes->addAttributeClass('bg-' . ($gradient === true ? 'gradient-' : '') . 'success');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function backgroundDanger($gradient = false)
    {
        $this->attributes->addAttributeClass('bg-' . ($gradient === true ? 'gradient-' : '') . 'danger');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function backgroundWarning($gradient = false)
    {
        $this->attributes->addAttributeClass('bg-' . ($gradient === true ? 'gradient-' : '') . 'warning');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function backgroundInfo($gradient = false)
    {
        $this->attributes->addAttributeClass('bg-' . ($gradient === true ? 'gradient-' : '') . 'info');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function backgroundLight($gradient = false)
    {
        $this->textDark();
        $this->attributes->addAttributeClass('bg-' . ($gradient === true ? 'gradient-' : '') . 'light');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function textDark()
    {
        $this->attributes->addAttributeClass('text-dark');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function backgroundDark($gradient = false)
    {
        $this->textLight();
        $this->attributes->addAttributeClass('bg-' . ($gradient === true ? 'gradient-' : '') . 'dark');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function textLight()
    {
        $this->attributes->addAttributeClass('text-light');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function backgroundWhite($gradient = false)
    {
        $this->attributes->addAttributeClass('bg-' . ($gradient === true ? 'gradient-' : '') . 'white');

        return $this;
    }
}