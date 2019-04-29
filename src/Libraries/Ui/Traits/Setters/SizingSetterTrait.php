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

namespace O2System\Framework\Libraries\Ui\Traits\Setters;

// ------------------------------------------------------------------------

/**
 * Trait SizingSetterTrait
 *
 * @package O2System\Framework\Libraries\Ui\Traits\Setters
 */
trait SizingSetterTrait
{
    /**
     * SizingSetterTrait::$sizingClassPrefix
     *
     * @var string|null
     */
    protected $sizingClassPrefix = null;

    // ------------------------------------------------------------------------

    /**
     * SizingSetterTrait::extraLargeSize
     *
     * @return static
     */
    public function extraLargeSize()
    {
        $this->setsizingClassSuffix('xl');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * SizingSetterTrait::setSizingClassSuffix
     *
     * @param string $suffix
     *
     * @return static
     */
    public function setSizingClassSuffix($suffix)
    {
        $this->attributes->addAttributeClass($this->sizingClassPrefix . '-' . $suffix);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * SizingSetterTrait::largeSize
     *
     * @return static
     */
    public function largeSize()
    {
        $this->setsizingClassSuffix('lg');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * SizingSetterTrait::mediumSize
     *
     * @return static
     */
    public function mediumSize()
    {
        $this->setsizingClassSuffix('m');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * SizingSetterTrait::smallSize
     *
     * @return static
     */
    public function smallSize()
    {
        $this->setsizingClassSuffix('sm');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * SizingSetterTrait::extraSmallSize
     *
     * @return static
     */
    public function extraSmallSize()
    {
        $this->setsizingClassSuffix('xs');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * SizingSetterTrait::setSizingClassPrefix
     *
     * @param string $prefix
     *
     * @return static
     */
    protected function setSizingClassPrefix($prefix)
    {
        $this->sizingClassPrefix = $prefix;

        return $this;
    }
}