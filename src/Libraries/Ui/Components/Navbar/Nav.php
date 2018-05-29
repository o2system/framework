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

namespace O2System\Framework\Libraries\Ui\Components\Navbar;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components\Dropdown;
use O2System\Framework\Libraries\Ui\Contents\Link;
use O2System\Framework\Libraries\Ui\Contents\Lists\Item;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Nav
 *
 * @package O2System\Framework\Libraries\Ui\Components\Navbar
 */
class Nav extends Unordered
{
    public function __construct()
    {
        parent::__construct();
        $this->attributes->addAttributeClass(['navbar-nav', 'mr-auto']);
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
                $list->toggle->attributes->addAttributeCLass('nav-link');
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
            $node->entity->setEntityName($list);
            $node->childNodes->push($list);
        }

        $node->attributes->addAttributeClass('nav-item');

        $this->pushChildNode($node);

        return $this->childNodes->last();
    }
}