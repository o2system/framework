<?php
/**
 * This file is part of the O2System Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */

// ------------------------------------------------------------------------

namespace O2System\Framework\Libraries\Ui\Admin\Sidebar;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Admin\Sidebar\Menu\UnorderedList;
use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Menu
 * @package O2System\Framework\Libraries\Ui\Admin\Sidebar
 */
class Menu extends Element
{
    /**
     * Menu::$list
     *
     * @var \O2System\Framework\Libraries\Ui\Admin\Sidebar\Menu\UnorderedList
     */
    public $list;

    // ------------------------------------------------------------------------

    /**
     * Code::__construct
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct('div');

        if (isset($attributes[ 'id' ])) {
            $this->entity->setEntityName($attributes[ 'id' ]);
        }

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $this->attributes->addAttribute($name, $value);
            }
        }

        $this->attributes->addAttributeClass('sidebar-menu');

        $this->list = new UnorderedList();

        $this->childNodes->push($this->list);
    }

    // ------------------------------------------------------------------------

    /**
     * Menu::createTitle
     *
     * @param string $title
     *
     * @return \O2System\Framework\Libraries\Ui\Contents\Lists\Item
     */
    public function createTitle($title)
    {
        $item = $this->list->createList($title);
        $item->attributes->addAttributeClass('menu-title');

        return $item;
    }

    // ------------------------------------------------------------------------

    /**
     * Menu::createItem
     *
     * @param mixed $item
     */
    public function createItem($item)
    {
        $item = $this->list->createList($item);
    }
}