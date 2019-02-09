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
 * Trait SpacingUtilitiesTrait
 * @package O2System\Framework\Libraries\Ui\Traits\Utilities
 */
trait SpacingUtilitiesTrait
{
    /**
     * SpacingUtilitiesTrait::margin
     *
     * @param int $pixel
     *
     * @return static
     */
    public function margin($pixel)
    {
        $this->attributes->addAttributeClass('m-' . (int)$pixel);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * SpacingUtilitiesTrait::marginTop
     *
     * @param int $pixel
     *
     * @return static
     */
    public function marginTop($pixel)
    {
        $this->attributes->addAttributeClass('mt-' . (int)$pixel);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * SpacingUtilitiesTrait::marginBottom
     *
     * @param int $pixel
     *
     * @return static
     */
    public function marginBottom($pixel)
    {
        $this->attributes->addAttributeClass('mb-' . (int)$pixel);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * SpacingUtilitiesTrait::marginTopBottom
     *
     * @param int $pixel
     *
     * @return static
     */
    public function marginTopBottom($pixel)
    {
        $this->attributes->addAttributeClass('my-' . (int)$pixel);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * SpacingUtilitiesTrait::marginLeft
     *
     * @param int $pixel
     *
     * @return static
     */
    public function marginLeft($pixel)
    {
        $this->attributes->addAttributeClass('ml-' . (int)$pixel);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * SpacingUtilitiesTrait::marginRight
     *
     * @param int $pixel
     *
     * @return static
     */
    public function marginRight($pixel)
    {
        $this->attributes->addAttributeClass('mr-' . (int)$pixel);

        return $this;
    }

    /**
     * SpacingUtilitiesTrait::marginLeftRight
     *
     * @param int $pixel
     * @return static
     */
    public function marginLeftRight($pixel)
    {
        $this->attributes->addAttributeClass('mx-' . (int)$pixel);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * SpacingUtilitiesTrait::marginAuto
     *
     * @return static
     */
    public function marginAuto()
    {
        $this->attributes->addAttributeClass('mx-auto');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * SpacingUtilitiesTrait::padding
     *
     * @param int $pixel
     *
     * @return static
     */
    public function padding($pixel)
    {
        $this->attributes->addAttributeClass('p-' . (int)$pixel);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * SpacingUtilitiesTrait::paddingTop
     *
     * @param int $pixel
     *
     * @return static
     */
    public function paddingTop($pixel)
    {
        $this->attributes->addAttributeClass('pt-' . (int)$pixel);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * SpacingUtilitiesTrait::paddingBottom
     *
     * @param int $pixel
     *
     * @return static
     */
    public function paddingBottom($pixel)
    {
        $this->attributes->addAttributeClass('pb-' . (int)$pixel);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * SpacingUtilitiesTrait::paddingTopBottom
     *
     * @param int $pixel
     *
     * @return static
     */
    public function paddingTopBottom($pixel)
    {
        $this->attributes->addAttributeClass('py-' . (int)$pixel);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * SpacingUtilitiesTrait::paddingLeft
     *
     * @param int $pixel
     * @return static
     */
    public function paddingLeft($pixel)
    {
        $this->attributes->addAttributeClass('pl-' . (int)$pixel);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * SpacingUtilitiesTrait::paddingRight
     *
     * @param int $pixel
     *
     * @return static
     */
    public function paddingRight($pixel)
    {
        $this->attributes->addAttributeClass('pr-' . (int)$pixel);

        return $this;
    }
    // ------------------------------------------------------------------------

    /**
     * SpacingUtilitiesTrait::paddingLeftRight
     *
     * @param int $pixel
     *
     * @return static
     */
    public function paddingLeftRight($pixel)
    {
        $this->attributes->addAttributeClass('px-' . (int)$pixel);

        return $this;
    }
}