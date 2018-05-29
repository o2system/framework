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

namespace O2System\Framework\Libraries\Ui\Components;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Contents;
use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Accordion
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class Accordion extends Element
{
    public function __construct($id = null)
    {
        parent::__construct('div', 'group');
        $this->attributes->addAttributeClass('accordion');
        $this->attributes->addAttribute('role', 'tablist');
        $this->attributes->addAttribute('aria-multiselectable', true);

        $id = empty($id) ? 'accordion-' . mt_rand(1, 1000) : $id;
        $this->attributes->setAttributeId($id);
    }

    public function createCard($title, $paragraph = null)
    {
        $collapseId = dash($title);
        $link = new Contents\Link($title, '#' . $collapseId);
        $link->attributes->addAttribute('data-toggle', 'collapse');
        $link->attributes->addAttribute('data-parent', '#' . $this->attributes->getAttributeId());

        $card = new \O2System\Framework\Libraries\Ui\Components\Accordion\Card();

        $card->header->attributes->addAttribute('role', 'tab');
        $card->header->childNodes->push($link);

        $block = $card->createBlock();
        $block->attributes->setAttributeId($collapseId);

        if (isset($paragraph)) {
            $block->setParagraph($paragraph);
        }

        if ($this->childNodes->count() > 0) {
            $card->hide();
        }

        $this->childNodes->push($card);

        return $this->childNodes->last();
    }
}