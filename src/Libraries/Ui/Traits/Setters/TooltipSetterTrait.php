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
 * Trait TooltipSetterTrait
 *
 * @package O2System\Framework\Libraries\Ui\Traits\Setters
 */
trait TooltipSetterTrait
{
    public function setTooltip($text, $placement = 'right')
    {
        $placement = in_array($placement, ['top', 'bottom', 'left', 'right']) ? $placement : 'right';

        $this->attributes->addAttribute('data-toggle', 'tooltip');
        $this->attributes->addAttribute('data-placement', $placement);
        $this->attributes->addAttribute('title', $text);

        return $this;
    }
}