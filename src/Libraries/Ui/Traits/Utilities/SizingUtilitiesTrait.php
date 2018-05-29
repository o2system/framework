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
 * Trait SizingUtilitiesTrait
 * @package O2System\Framework\Libraries\Ui\Traits\Utilities
 */
trait SizingUtilitiesTrait
{
    public function width($width)
    {
        $this->attributes->addAttributeClass('w-' . (int)$width);

        return $this;
    }

    public function maxWidth($maxWidth)
    {
        $this->attributes->addAttributeClass('mw-' . (int)$maxWidth);

        return $this;
    }

    public function maxHeight($maxHeight)
    {
        $this->attributes->addAttributeClass('mh-' . (int)$maxHeight);

        return $this;
    }
}