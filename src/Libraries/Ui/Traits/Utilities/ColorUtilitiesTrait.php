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

namespace O2System\Framework\Libraries\Ui\Traits\Utilities;

// ------------------------------------------------------------------------

namespace O2System\Framework\Libraries\Ui\Traits\Utilities;

/**
 * Trait ColorUtilitiesTrait
 * @package O2System\Framework\Libraries\Ui\Traits\Utilities
 */
trait ColorUtilitiesTrait
{
    /**
     * ColorUtilitiesTrait::textPrimary
     *
     * @return static
     */
    public function textPrimary()
    {
        $this->attributes->addAttributeClass('text-primary');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ColorUtilitiesTrait::textSecondary
     *
     * @return static
     */
    public function textSecondary()
    {
        $this->attributes->addAttributeClass('text-secondary');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ColorUtilitiesTrait::textSuccess
     *
     * @return static
     */
    public function textSuccess()
    {
        $this->attributes->addAttributeClass('text-success');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ColorUtilitiesTrait::textDanger
     *
     * @return static
     */
    public function textDanger()
    {
        $this->attributes->addAttributeClass('text-danger');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ColorUtilitiesTrait::textWarning
     *
     * @return static
     */
    public function textWarning()
    {
        $this->attributes->addAttributeClass('text-warning');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ColorUtilitiesTrait::textInfo
     *
     * @return static
     */
    public function textInfo()
    {
        $this->attributes->addAttributeClass('text-info');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ColorUtilitiesTrait::textMuted
     *
     * @return static
     */
    public function textMuted()
    {
        $this->attributes->addAttributeClass('text-muted');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ColorUtilitiesTrait::textWhite
     *
     * @return static
     */
    public function textWhite()
    {
        $this->attributes->addAttributeClass('text-white');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ColorUtilitiesTrait::backgroundPrimary
     *
     * @param bool $gradient
     *
     * @return static
     */
    public function backgroundPrimary($gradient = false)
    {
        $this->attributes->addAttributeClass('bg-' . ($gradient === true ? 'gradient-' : '') . 'primary');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ColorUtilitiesTrait::backgroundSecondary
     *
     * @return static
     */
    public function backgroundSecondary($gradient = false)
    {
        $this->attributes->addAttributeClass('bg-' . ($gradient === true ? 'gradient-' : '') . 'secondary');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ColorUtilitiesTrait::backgroundDanger
     *
     * @param bool $gradient
     *
     * @return static
     */
    public function backgroundSuccess($gradient = false)
    {
        $this->attributes->addAttributeClass('bg-' . ($gradient === true ? 'gradient-' : '') . 'success');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ColorUtilitiesTrait::backgroundDanger
     *
     * @param bool $gradient
     *
     * @return static
     */
    public function backgroundDanger($gradient = false)
    {
        $this->attributes->addAttributeClass('bg-' . ($gradient === true ? 'gradient-' : '') . 'danger');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ColorUtilitiesTrait::backgroundWarning
     *
     * @param bool $gradient
     *
     * @return static
     */
    public function backgroundWarning($gradient = false)
    {
        $this->attributes->addAttributeClass('bg-' . ($gradient === true ? 'gradient-' : '') . 'warning');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ColorUtilitiesTrait::backgroundInfo
     *
     * @param bool $gradient
     *
     * @return static
     */
    public function backgroundInfo($gradient = false)
    {
        $this->attributes->addAttributeClass('bg-' . ($gradient === true ? 'gradient-' : '') . 'info');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ColorUtilitiesTrait::backgroundLight
     *
     * @param bool $gradient
     *
     * @return static
     */
    public function backgroundLight($gradient = false)
    {
        $this->textDark();
        $this->attributes->addAttributeClass('bg-' . ($gradient === true ? 'gradient-' : '') . 'light');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ColorUtilitiesTrait::textDark
     *
     * @return static
     */
    public function textDark()
    {
        $this->attributes->addAttributeClass('text-dark');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ColorUtilitiesTrait::backgroundDark
     *
     * @param bool $gradient
     *
     * @return static
     */
    public function backgroundDark($gradient = false)
    {
        $this->textLight();
        $this->attributes->addAttributeClass('bg-' . ($gradient === true ? 'gradient-' : '') . 'dark');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ColorUtilitiesTrait::textLight
     *
     * @return static
     */
    public function textLight()
    {
        $this->attributes->addAttributeClass('text-light');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ColorUtilitiesTrait::backgroundWhite
     *
     * @param bool $gradient
     *
     * @return static
     */
    public function backgroundWhite($gradient = false)
    {
        $this->attributes->addAttributeClass('bg-' . ($gradient === true ? 'gradient-' : '') . 'white');

        return $this;
    }
}