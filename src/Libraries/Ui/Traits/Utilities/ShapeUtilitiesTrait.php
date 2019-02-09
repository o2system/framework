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
 * Trait ShapeSetterTrait
 * @package O2System\Framework\Libraries\Ui\Traits\Utilities
 */
trait ShapeUtilitiesTrait
{
    /**
     * ShapeUtilitiesTrait::rounded
     *
     * @return static
     */
    public function rounded()
    {
        $this->attributes->removeAttributeClass(['rounded', 'rounded-*']);
        $this->attributes->addAttributeClass('rounded');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ShapeUtilitiesTrait::roundedTop
     *
     * @return static
     */
    public function roundedTop()
    {
        $this->attributes->removeAttributeClass(['rounded', 'rounded-*']);
        $this->attributes->addAttributeClass('rounded-top');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ShapeUtilitiesTrait::roundedBottom
     *
     * @return static
     */
    public function roundedBottom()
    {
        $this->attributes->removeAttributeClass(['rounded', 'rounded-*']);
        $this->attributes->addAttributeClass('rounded-bottom');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ShapeUtilitiesTrait::roundedLeft
     *
     * @return static
     */
    public function roundedLeft()
    {
        $this->attributes->removeAttributeClass(['rounded', 'rounded-*']);
        $this->attributes->addAttributeClass('rounded-left');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ShapeUtilitiesTrait::roundedRight
     *
     * @return static
     */
    public function roundedRight()
    {
        $this->attributes->removeAttributeClass(['rounded', 'rounded-*']);
        $this->attributes->addAttributeClass('rounded-right');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ShapeUtilitiesTrait::circle
     *
     * @return static
     */
    public function circle()
    {
        $this->attributes->removeAttributeClass(['rounded', 'rounded-*']);
        $this->attributes->addAttributeClass('rounded-circle');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ShapeUtilitiesTrait::square
     *
     * @return static
     */
    public function square()
    {
        $this->attributes->removeAttributeClass(['rounded', 'rounded-*']);
        $this->attributes->addAttributeClass('rounded-0');

        return $this;
    }
}