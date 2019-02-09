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
 * Trait PositionUtilitiesTrait
 * @package O2System\Framework\Libraries\Ui\Traits\Utilities
 */
trait PositionUtilitiesTrait
{
    /**
     * PositionUtilitiesTrait::positionStatic
     *
     * @return static
     */
    public function positionStatic()
    {
        $this->attributes->addAttributeClass('position-static');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * PositionUtilitiesTrait::positionRelative
     *
     * @return static
     */
    public function positionRelative()
    {
        $this->attributes->addAttributeClass('position-relative');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * PositionUtilitiesTrait::positionAbsolute
     *
     * @return static
     *
     */
    public function positionAbsolute()
    {
        $this->attributes->addAttributeClass('position-absolute');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * PositionUtilitiesTrait::positionFixed
     *
     * @return static
     */
    public function positionFixed()
    {
        $this->attributes->addAttributeClass('position-fixed');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * PositionUtilitiesTrait::positionSticky
     *
     * @return static
     */
    public function positionSticky()
    {
        $this->attributes->addAttributeClass('position-sticky');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * PositionUtilitiesTrait::positionFixedTop
     *
     * @return static
     */
    public function positionFixedTop()
    {
        $this->attributes->addAttributeClass('fixed-top');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * PositionUtilitiesTrait::positionFixedBottom
     *
     * @return static
     */
    public function positionFixedBottom()
    {
        $this->attributes->addAttributeClass('fixed-bottom');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * PositionUtilitiesTrait::positionFixedTop
     *
     * @return static
     */
    public function positionStickyTop()
    {
        $this->attributes->addAttributeClass('sticky-top');

        return $this;
    }
}