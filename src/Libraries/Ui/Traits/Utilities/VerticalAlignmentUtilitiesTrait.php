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
 * Trait VerticalAlignmentUtilitiesTrait
 * @package O2System\Framework\Libraries\Ui\Traits\Utilities
 */
trait VerticalAlignmentUtilitiesTrait
{
    /**
     * VerticalAlignmentUtilitiesTrait::alignBaseline
     *
     * @return static
     */
    public function alignBaseline()
    {
        $this->attributes->addAttributeClass('align-baseline');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * VerticalAlignmentUtilitiesTrait::alignTop
     *
     * @return static
     */
    public function alignTop()
    {
        $this->attributes->addAttributeClass('align-top');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * VerticalAlignmentUtilitiesTrait::alignMiddle
     *
     * @return static
     */
    public function alignMiddle()
    {
        $this->attributes->addAttributeClass('align-middle');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * VerticalAlignmentUtilitiesTrait::alignBottom
     *
     * @return static
     */
    public function alignBottom()
    {
        $this->attributes->addAttributeClass('align-bottom');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * VerticalAlignmentUtilitiesTrait::alignTextTop
     *
     * @return static
     */
    public function alignTextTop()
    {
        $this->attributes->addAttributeClass('align-text-top');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * VerticalAlignmentUtilitiesTrait::alignTextBottom
     *
     * @return static
     */
    public function alignTextBottom()
    {
        $this->attributes->addAttributeClass('align-text-bottom');

        return $this;
    }

}