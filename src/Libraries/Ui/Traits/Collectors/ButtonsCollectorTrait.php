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

namespace O2System\Framework\Libraries\Ui\Traits\Collectors;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components\Button;
use O2System\Spl\Iterators\ArrayIterator;

/**
 * Class ButtonsCollectorTrait
 *
 * @package O2System\Framework\Libraries\Ui\Traits\Collectors
 */
trait ButtonsCollectorTrait
{
    public $buttons;

    public function hasButtons()
    {
        if ($this->buttons instanceof ArrayIterator) {
            if ($this->buttons->count()) {
                return true;
            }
        }

        return false;
    }

    public function createButton($label, array $attributes = [], $contextualClass = Button::DEFAULT_CONTEXT)
    {
        $button = new Button($label, $attributes, $contextualClass);

        if ( ! $this->buttons instanceof ArrayIterator) {
            $this->buttons = new ArrayIterator();
        }

        $this->buttons->push($button);

        return $this->buttons->last();
    }

    public function addButton(Button $button)
    {
        if ( ! $this->buttons instanceof ArrayIterator) {
            $this->buttons = new ArrayIterator();
        }

        $this->buttons->push($button);

        return $this;
    }
}