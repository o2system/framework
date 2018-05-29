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
 * Trait ShapeSetterTrait
 * @package O2System\Framework\Libraries\Ui\Traits\Utilities
 */
trait ShapeUtilitiesTrait
{
    public function rounded()
    {
        $this->attributes->removeAttributeClass(['rounded', 'rounded-*']);
        $this->attributes->addAttributeClass('rounded');

        return $this;
    }

    public function roundedTop()
    {
        $this->attributes->removeAttributeClass(['rounded', 'rounded-*']);
        $this->attributes->addAttributeClass('rounded-top');

        return $this;
    }

    public function roundedBottom()
    {
        $this->attributes->removeAttributeClass(['rounded', 'rounded-*']);
        $this->attributes->addAttributeClass('rounded-bottom');

        return $this;
    }

    public function roundedLeft()
    {
        $this->attributes->removeAttributeClass(['rounded', 'rounded-*']);
        $this->attributes->addAttributeClass('rounded-left');

        return $this;
    }

    public function roundedRight()
    {
        $this->attributes->removeAttributeClass(['rounded', 'rounded-*']);
        $this->attributes->addAttributeClass('rounded-right');

        return $this;
    }

    public function circle()
    {
        $this->attributes->removeAttributeClass(['rounded', 'rounded-*']);
        $this->attributes->addAttributeClass('rounded-circle');

        return $this;
    }

    public function square()
    {
        $this->attributes->removeAttributeClass(['rounded', 'rounded-*']);
        $this->attributes->addAttributeClass('rounded-0');

        return $this;
    }
}