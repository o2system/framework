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

namespace O2System\Framework\Libraries\Ui\Components\Dropdown;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Contents\Link;
use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Nav
 *
 * @package O2System\Framework\Libraries\Ui\Components\Dropdown
 */
class Menu extends Element
{
    /**
     * Menu::__construct
     */
    public function __construct()
    {
        parent::__construct('div', 'menu');
        $this->attributes->addAttributeClass('dropdown-menu');
    }

    // ------------------------------------------------------------------------

    /**
     * Menu::createHeader
     *
     * @param string $text
     * @param string $tagName
     *
     * @return Element
     */
    public function createHeader($text, $tagName = 'h6')
    {
        $header = new Element($tagName);
        $header->attributes->addAttributeClass('dropdown-header');
        $header->textContent->push($text);

        $this->childNodes->push($header);

        return $this->childNodes->last();
    }

    // ------------------------------------------------------------------------

    /**
     * Menu::createItem
     *
     * @param string|null $label
     * @param string|null $href
     *
     * @return Link
     */
    public function createItem($label = null, $href = null)
    {
        $link = new Link($label, $href);
        $link->attributes->addAttributeClass('dropdown-item');

        $this->childNodes->push($link);

        return $this->childNodes->last();
    }

    // ------------------------------------------------------------------------

    /**
     * Menu::createDivider
     *
     * @return Element
     */
    public function createDivider()
    {
        $element = new Element('div');
        $element->attributes->addAttributeClass('dropdown-divider');

        $this->childNodes->push($element);

        return $this->childNodes->last();
    }
}