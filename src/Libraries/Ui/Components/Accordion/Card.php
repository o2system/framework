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

namespace O2System\Framework\Libraries\Ui\Components\Accordion;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components\Accordion\Card\Body;

/**
 * Class Card
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class Card extends \O2System\Framework\Libraries\Ui\Components\Card
{
    public $block;

    public function createBlock()
    {
        $this->childNodes->push(new Body());

        return $this->block = $this->childNodes->last();
    }

    public function show()
    {
        $this->block->collapse->attributes->removeAttributeClass('hide');
        $this->block->collapse->attributes->addAttributeClass('show');

        return $this;
    }

    public function hide()
    {
        $this->block->collapse->attributes->removeAttributeClass('show');
        $this->block->collapse->attributes->addAttributeClass('hide');

        return $this;
    }

    public function render()
    {
        $output[] = $this->open();

        if ($this->header->hasTextContent() || $this->header->hasChildNodes()) {
            $output[] = $this->header;
        }

        if ($this->hasChildNodes()) {
            $output[] = implode(PHP_EOL, $this->childNodes->getArrayCopy());
        }

        if ($this->footer->hasTextContent() || $this->footer->hasChildNodes()) {
            $output[] = $this->footer;
        }

        $output[] = $this->close();

        return implode(PHP_EOL, $output);
    }
}