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
 * Trait FloatUtilitiesTrait
 * @package O2System\Framework\Libraries\Ui\Traits\Utilities
 */
trait FloatUtilitiesTrait
{
    public function floatLeft($size = null)
    {
        if (empty($size)) {
            $this->attributes->addAttributeClass('float-left');
        } elseif (in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'])) {
            $this->attributes->addAttributeClass('float-' . $size . '-left');
        }

        return $this;
    }

    public function floatRight($size = null)
    {
        if (empty($size)) {
            $this->attributes->addAttributeClass('float-right');
        } elseif (in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'])) {
            $this->attributes->addAttributeClass('float-' . $size . '-right');
        }

        return $this;
    }

    public function floatNone($size = null)
    {
        if (empty($size)) {
            $this->attributes->addAttributeClass('float-none');
        } elseif (in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'])) {
            $this->attributes->addAttributeClass('float-' . $size . '-none');
        }

        return $this;
    }
}