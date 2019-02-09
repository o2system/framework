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

/**
 * Trait BorderSetterTrait
 *
 * @package O2System\Framework\Libraries\Ui\Traits\Utilities
 */
trait BorderUtilitiesTrait
{
    /**
     * BorderUtilitiesTrait::removeBorder
     *
     * @return static
     */
    public function removeBorder()
    {
        $this->attributes->addAttributeClass('border-0');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * BorderUtilitiesTrait::removeBorderTop
     *
     * @return static
     */
    public function removeBorderTop()
    {
        $this->attributes->addAttributeClass('border-top-0');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * BorderUtilitiesTrait::removeBorderBottom
     *
     * @return static
     */
    public function removeBorderBottom()
    {
        $this->attributes->addAttributeClass('border-bottom-0');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * BorderUtilitiesTrait::removeBorderLeft
     *
     * @return static
     */
    public function removeBorderLeft()
    {
        $this->attributes->addAttributeClass('border-left-0');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * BorderUtilitiesTrait::removeBorderRight
     *
     * @return static
     *
     */
    public function removeBorderRight()
    {
        $this->attributes->addAttributeClass('border-right-0');
    }

    // ------------------------------------------------------------------------

    /**
     * BorderUtilitiesTrait::contextBorderDefault
     *
     * @return static
     */
    public function contextBorderDefault()
    {
        $this->attributes->addAttributeClass('border-default');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * BorderUtilitiesTrait::contextBorderPrimary
     *
     * @return static
     */
    public function contextBorderPrimary()
    {
        $this->attributes->addAttributeClass('border-primary');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * BorderUtilitiesTrait::contextBorderSecondary
     *
     * @return static
     */
    public function contextBorderSecondary()
    {
        $this->attributes->addAttributeClass('border-secondary');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * BorderUtilitiesTrait::contextBorderSuccess
     *
     * @return static
     */
    public function contextBorderSuccess()
    {
        $this->attributes->addAttributeClass('border-success');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * BorderUtilitiesTrait::contextBorderInfo
     *
     * @return static
     */
    public function contextBorderInfo()
    {
        $this->attributes->addAttributeClass('border-info');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * BorderUtilitiesTrait::contextBorderWarning
     *
     * @return static
     */
    public function contextBorderWarning()
    {
        $this->attributes->addAttributeClass('border-warning');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * BorderUtilitiesTrait::contextBorderDanger
     *
     * @return static
     */
    public function contextBorderDanger()
    {
        $this->attributes->addAttributeClass('border-danger');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * BorderUtilitiesTrait::contextBorderLight
     *
     * @return static
     */
    public function contextBorderLight()
    {
        $this->attributes->addAttributeClass('border-light');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * BorderUtilitiesTrait::contextBorderDark
     *
     * @return static
     */
    public function contextBorderDark()
    {
        $this->attributes->addAttributeClass('border-dark');

        return $this;
    }
}