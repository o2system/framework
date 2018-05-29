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

namespace O2System\Framework\Libraries\Ui\Components\Panel;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Header
 *
 * @package O2System\Framework\Libraries\Ui\Components\Card
 */
class Header extends Element
{
    public function __construct()
    {
        parent::__construct('div', 'heading');
        $this->attributes->addAttributeClass('panel-heading');
    }

    public function createTitle($text, $tagName = 'h3')
    {
        $node = new Element($tagName);
        $node->attributes->addAttributeClass('panel-title');

        if ($text instanceof Element) {
            $node->entity->setEntityName($text->entity->getEntityName());
            $node->childNodes->push($text);
        } else {
            $text = trim($text);
            $node->entity->setEntityName('title-' . md5($text));
            $node->textContent->push($text);
        }

        $this->childNodes->push($node);

        return $this->childNodes->last();
    }
}