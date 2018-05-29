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
 * Trait PopoverSetterTrait
 *
 * @package O2System\Framework\Libraries\Ui\Traits\Setters
 */
trait PopoverSetterTrait
{
    public function setPopover($title, $content)
    {
        $this->attributes->addAttribute('data-toggle', 'popover');
        $this->attributes->addAttribute('title', $title);
        $this->attributes->addAttribute('data-content', $content);

        return $this;
    }
}