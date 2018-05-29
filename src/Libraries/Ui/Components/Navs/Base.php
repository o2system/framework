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

namespace O2System\Framework\Libraries\Ui\Components\Navs;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components\Dropdown;
use O2System\Framework\Libraries\Ui\Contents\Link;
use O2System\Framework\Libraries\Ui\Contents\Lists\Item;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Base
 *
 * @package O2System\Framework\Libraries\Ui\Components\Navs
 */
class Base extends Unordered
{
    public function __construct()
    {
        parent::__construct();

        $this->attributes->addAttributeClass('nav');
    }

    public function createLink($label, $href = null)
    {
        $link = new Link($label, $href);
        $link->attributes->addAttributeClass('nav-link');

        return $this->createList($link);
    }

    public function createList($list = null)
    {
        $node = new Item();

        if ($list instanceof Item) {
            $node = $list;
        } elseif ($list instanceof Element) {
            $node->entity->setEntityName($list->entity->getEntityName());

            if ($list instanceof Dropdown) {
                $list = clone $list;
                $node->attributes->addAttributeClass('dropdown');

                $list->toggle->tagName = 'a';
                $list->toggle->attributes->remove('type');
                $list->toggle->attributes->removeAttributeClass(['btn', 'btn-*']);
                $list->toggle->attributes->addAttribute('role', 'button');

                $node->childNodes->push($list->toggle);
                $node->childNodes->push($list->menu);
            } else {
                $node->childNodes->push($list);
            }

        } else {
            if (is_numeric($list)) {
                $node->entity->setEntityName('list-' . $list);
            } else {
                $node->entity->setEntityName($list);
            }
            $node->textContent->push($list);
        }

        $node->attributes->addAttributeClass('nav-item');
        $node->attributes->addAttribute('role', 'presentation');

        $this->pushChildNode($node);

        return $this->childNodes->last();
    }

    public function fill()
    {
        $this->attributes->addAttributeClass('nav-fill');

        return $this;
    }

    public function vertical()
    {
        $this->attributes->addAttributeClass('flex-column');

        return $this;
    }
}