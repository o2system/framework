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
 * Trait SizingSetterTrait
 *
 * @package O2System\Framework\Libraries\Ui\Traits\Setters
 */
trait SizingSetterTrait
{

    protected $sizingClassPrefix = null;

    // ------------------------------------------------------------------------

    public function extraLargeSize()
    {
        $this->setsizingClassSuffix('xl');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function setSizingClassSuffix($suffix)
    {
        $this->attributes->addAttributeClass($this->sizingClassPrefix . '-' . $suffix);

        return $this;
    }

    public function largeSize()
    {
        $this->setsizingClassSuffix('lg');

        return $this;
    }

    public function mediumSize()
    {
        $this->setsizingClassSuffix('m');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function smallSize()
    {
        $this->setsizingClassSuffix('sm');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function extraSmallSize()
    {
        $this->setsizingClassSuffix('xs');

        return $this;
    }

    // ------------------------------------------------------------------------

    protected function setSizingClassPrefix($prefix)
    {
        $this->sizingClassPrefix = $prefix;

        return $this;
    }
}