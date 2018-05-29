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
 * Trait DisplayUtilitiesTrait
 * @package O2System\Framework\Libraries\Ui\Traits\Utilities
 */
trait DisplayUtilitiesTrait
{
    public function displayNone($size = null)
    {
        if (empty($size)) {
            $this->attributes->addAttributeClass('d-none');
        } elseif (in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'])) {
            $this->attributes->addAttributeClass('d-' . $size . '-none');
        }

        return $this;
    }

    public function displayInline($size = null)
    {
        if (empty($size)) {
            $this->attributes->addAttributeClass('d-inline');
        } elseif (in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'])) {
            $this->attributes->addAttributeClass('d-' . $size . '-inline');
        }

        return $this;
    }

    public function displayInlineBlock($size = null)
    {
        if (empty($size)) {
            $this->attributes->addAttributeClass('d-inline-block');
        } elseif (in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'])) {
            $this->attributes->addAttributeClass('d-' . $size . '-inline-block');
        }

        return $this;
    }

    public function displayBlock($size = null)
    {
        if (empty($size)) {
            $this->attributes->addAttributeClass('d-block');
        } elseif (in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'])) {
            $this->attributes->addAttributeClass('d-' . $size . '-block');
        }

        return $this;
    }

    public function displayTable($size = null)
    {
        if (empty($size)) {
            $this->attributes->addAttributeClass('d-table');
        } elseif (in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'])) {
            $this->attributes->addAttributeClass('d-' . $size . '-table');
        }

        return $this;
    }

    public function displayTableCell($size = null)
    {
        if (empty($size)) {
            $this->attributes->addAttributeClass('d-table-cell');
        } elseif (in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'])) {
            $this->attributes->addAttributeClass('d-' . $size . '-table-cell');
        }

        return $this;
    }

    public function displayFlex($size = null)
    {
        if (empty($size)) {
            $this->attributes->addAttributeClass('d-flex');
        } elseif (in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'])) {
            $this->attributes->addAttributeClass('d-' . $size . '-flex');
        }

        return $this;
    }

    public function displayInlineFlex($size = null)
    {
        if (empty($size)) {
            $this->attributes->addAttributeClass('d-inline-flex');
        } elseif (in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'])) {
            $this->attributes->addAttributeClass('d-' . $size . '-inline-flex');
        }

        return $this;
    }

    public function displayPrintBlock()
    {
        $this->attributes->addAttributeClass('d-print-block');

        return $this;
    }

    public function displayPrintInline()
    {
        $this->attributes->addAttributeClass('d-print-inline');

        return $this;
    }

    public function displayPrintInlineBlock()
    {
        $this->attributes->addAttributeClass('d-print-inline-block');

        return $this;
    }

    public function displayPrintNone()
    {
        $this->attributes->addAttributeClass('d-print-none');

        return $this;
    }
}