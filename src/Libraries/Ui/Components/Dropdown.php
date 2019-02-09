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

namespace O2System\Framework\Libraries\Ui\Components;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components\Dropdown\Menu;
use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Dropdown
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class Dropdown extends Element
{
    /**
     * Dropdown::$toggle
     *
     * @var \O2System\Framework\Libraries\Ui\Components\Button
     */
    public $toggle;

    /**
     * Dropdown::$toggleButton
     *
     * @var \O2System\Framework\Libraries\Ui\Components\Button
     */
    public $toggleButton;

    /**
     * Dropdown::$menu
     *
     * @var \O2System\Framework\Libraries\Ui\Components\Dropdown\Menu
     */
    public $menu;

    /**
     * Dropdown::$splitMenu
     *
     * @var bool
     */
    protected $splitMenu = false;

    // ------------------------------------------------------------------------

    /**
     * Dropdown::__construct
     *
     * @param string|null $label
     */
    public function __construct($label = null)
    {
        parent::__construct('div');
        $this->attributes->addAttributeClass('dropdown');

        $this->setToggle($label);
        $this->menu = new Menu();
    }

    // ------------------------------------------------------------------------

    /**
     * Dropdown::setToggle
     *
     * @param string|Button $label
     *
     * @return static
     */
    public function setToggle($label)
    {
        if ($label instanceof Button) {
            $this->toggle = $label;
        } else {
            $this->toggle = new Button($label);
            $this->entity->setEntityName($label);
        }

        $this->toggle->attributes->addAttribute('data-toggle', 'dropdown');
        $this->toggle->attributes->addAttribute('aria-haspopup', true);
        $this->toggle->attributes->addAttribute('aria-expanded', false);
        $this->toggle->attributes->addAttributeClass('dropdown-toggle');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Dropdown::setAlignment
     *
     * @param string $alignment
     *
     * @return static
     */
    public function setAlignment($alignment)
    {
        $this->attributes->addAttributeClass('dropdown-menu-' . $alignment);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Dropdown::dropup
     *
     * @return static
     */
    public function dropup()
    {
        $this->attributes->removeAttributeClass('dropdown');
        $this->attributes->addAttributeClass('dropup');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Dropdown::splitMenu
     *
     * @return static
     */
    public function splitMenu()
    {
        $this->attributes->removeAttributeClass('dropdown');
        $this->attributes->addAttributeClass('btn-group');

        $textContent = clone $this->toggle->textContent;
        $childNodes = clone $this->toggle->childNodes;
        $attributes = $this->toggle->attributes;

        $this->toggle = new Button();
        $buttonAttributes = clone $attributes;
        $buttonAttributes->removeAttributeClass('dropdown-toggle');
        $buttonAttributes->removeAttribute(['data-*', 'aria-*']);
        $this->toggle->attributes = $buttonAttributes;

        $this->toggle->textContent = $textContent;

        $this->toggleButton = new Button();
        $this->toggleButton->attributes = $attributes;
        $this->toggleButton->childNodes = $childNodes;

        $srOnly = new Element('span');
        $srOnly->attributes->addAttributeClass('sr-only');
        $srOnly->textContent->push('Toggle Dropdown');
        $this->toggleButton->childNodes->append($srOnly);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Dropdown::render
     *
     * @return string
     */
    public function render()
    {
        $output[] = $this->open();
        $output[] = $this->toggle;

        if ($this->toggleButton instanceof Button) {
            $output[] = $this->toggleButton;
        }

        $output[] = $this->menu;
        $output[] = $this->close();

        return implode(PHP_EOL, $output);
    }
}