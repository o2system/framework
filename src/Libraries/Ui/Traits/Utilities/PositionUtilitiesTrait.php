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
 * Trait PositionUtilitiesTrait
 * @package O2System\Framework\Libraries\Ui\Traits\Utilities
 */
trait PositionUtilitiesTrait
{
    public function positionStatic()
    {
        $this->attributes->addAttributeClass('position-static');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function positionRelative()
    {
        $this->attributes->addAttributeClass('position-relative');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function positionAbsolute()
    {
        $this->attributes->addAttributeClass('position-absolute');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function positionFixed()
    {
        $this->attributes->addAttributeClass('position-fixed');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function positionSticky()
    {
        $this->attributes->addAttributeClass('position-sticky');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function positionFixedTop()
    {
        $this->attributes->addAttributeClass('fixed-top');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function positionFixedBottom()
    {
        $this->attributes->addAttributeClass('fixed-bottom');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function positionStickyTop()
    {
        $this->attributes->addAttributeClass('sticky-top');

        return $this;
    }
}