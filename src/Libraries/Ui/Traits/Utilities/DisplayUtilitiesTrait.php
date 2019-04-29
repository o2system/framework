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
 * Trait DisplayUtilitiesTrait
 * @package O2System\Framework\Libraries\Ui\Traits\Utilities
 */
trait DisplayUtilitiesTrait
{
    /**
     * DisplayUtilitiesTrait::displayNone
     *
     * @param string|null $size
     *
     * @return static
     */
    public function displayNone($size = null)
    {
        if (empty($size)) {
            $this->attributes->addAttributeClass('d-none');
        } elseif (in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'])) {
            $this->attributes->addAttributeClass('d-' . $size . '-none');
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * DisplayUtilitiesTrait::displayInline
     *
     * @param string|null $size
     *
     * @return static
     */
    public function displayInline($size = null)
    {
        if (empty($size)) {
            $this->attributes->addAttributeClass('d-inline');
        } elseif (in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'])) {
            $this->attributes->addAttributeClass('d-' . $size . '-inline');
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * DisplayUtilitiesTrait::displayInlineBlock
     *
     * @param string|null $size
     *
     * @return static
     */
    public function displayInlineBlock($size = null)
    {
        if (empty($size)) {
            $this->attributes->addAttributeClass('d-inline-block');
        } elseif (in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'])) {
            $this->attributes->addAttributeClass('d-' . $size . '-inline-block');
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * DisplayUtilitiesTrait::displayBlock
     *
     * @param string|null $size
     *
     * @return static
     */
    public function displayBlock($size = null)
    {
        if (empty($size)) {
            $this->attributes->addAttributeClass('d-block');
        } elseif (in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'])) {
            $this->attributes->addAttributeClass('d-' . $size . '-block');
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * DisplayUtilitiesTrait::displayTable
     *
     * @param string|null $size
     *
     * @return static
     */
    public function displayTable($size = null)
    {
        if (empty($size)) {
            $this->attributes->addAttributeClass('d-table');
        } elseif (in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'])) {
            $this->attributes->addAttributeClass('d-' . $size . '-table');
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * DisplayUtilitiesTrait::displayTableCell
     *
     * @param string|null $size
     *
     * @return static
     */
    public function displayTableCell($size = null)
    {
        if (empty($size)) {
            $this->attributes->addAttributeClass('d-table-cell');
        } elseif (in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'])) {
            $this->attributes->addAttributeClass('d-' . $size . '-table-cell');
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * DisplayUtilitiesTrait::displayFlex
     *
     * @param string|null $size
     *
     * @return static
     */
    public function displayFlex($size = null)
    {
        if (empty($size)) {
            $this->attributes->addAttributeClass('d-flex');
        } elseif (in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'])) {
            $this->attributes->addAttributeClass('d-' . $size . '-flex');
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * DisplayUtilitiesTrait::displayInlineFlex
     *
     * @param string|null $size
     *
     * @return static
     */
    public function displayInlineFlex($size = null)
    {
        if (empty($size)) {
            $this->attributes->addAttributeClass('d-inline-flex');
        } elseif (in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'])) {
            $this->attributes->addAttributeClass('d-' . $size . '-inline-flex');
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * DisplayUtilitiesTrait::displayPrintBlock
     *
     * @return static
     */
    public function displayPrintBlock()
    {
        $this->attributes->addAttributeClass('d-print-block');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * DisplayUtilitiesTrait::displayPrintInline
     *
     * @return static
     */
    public function displayPrintInline()
    {
        $this->attributes->addAttributeClass('d-print-inline');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * DisplayUtilitiesTrait::displayPrintInlineBlock
     *
     * @return static
     */
    public function displayPrintInlineBlock()
    {
        $this->attributes->addAttributeClass('d-print-inline-block');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * DisplayUtilitiesTrait::displayPrintNone
     *
     * @return static
     */
    public function displayPrintNone()
    {
        $this->attributes->addAttributeClass('d-print-none');

        return $this;
    }
}