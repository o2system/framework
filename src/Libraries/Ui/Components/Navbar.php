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

use O2System\Framework\Libraries\Ui\Components\Card\Header\Nav\Link;
use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Navbar
 *
 * @todo    : add bgsetter trait
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class Navbar extends Element
{
    public $brand;
    public $collapse;

    /**
     * @var Navbar\Nav
     */
    public $nav;

    /**
     * @var Form
     */
    public $form;

    public function __construct($id = null)
    {
        $id = empty($id) ? 'navbar-' . mt_rand(0, 100) : $id;

        parent::__construct('nav', 'navbar');
        $this->attributes->addAttributeClass(['navbar', 'navbar-expand-lg', 'navbar-light']);

        // Toogle Button
        $button = new Button();
        $button->attributes->addAttributeClass(['navbar-toggler', 'navbar-toggler-right']);
        $button->attributes->addAttribute('type', 'button');
        $button->attributes->addAttribute('data-toggle', 'collapse');
        $button->attributes->addAttribute('data-target', '#' . $id);
        $button->attributes->addAttribute('aria-controls', $id);
        $button->attributes->addAttribute('aria-expanded', false);
        $button->attributes->addAttribute('aria-label', 'Toggle navigation');

        $span = new Element('span');
        $span->attributes->addAttributeClass('navbar-toggler-icon');

        $button->childNodes->push($span);

        $this->childNodes->push($button);

        // Collapse
        $collapse = new Element('div', 'collapse');
        $collapse->attributes->addAttributeClass(['collapse', 'navbar-collapse']);
        $collapse->attributes->setAttributeId($id);
        $collapse->childNodes->push(new Navbar\Nav());

        $this->nav = $collapse->childNodes->last();

        $this->childNodes->push($collapse);
        $this->collapse = $this->childNodes->last();
    }

    /**
     * @param $label
     *
     * @return \O2System\Framework\Libraries\Ui\Contents\Link
     */
    public function createBrand($label)
    {
        $brand = new Link();
        $brand->attributes->addAttributeClass('navbar-brand');
        $brand->setAttributeHref(base_url());

        if (is_object($label)) {
            $brand->childNodes->push($label);
        } else {
            $brand->textContent->push($label);
        }

        $this->childNodes->unshift($brand);

        return $this->brand = $this->childNodes->last();
    }

    /**
     * @return \O2System\Framework\Libraries\Ui\Components\Navbar\Form
     */
    public function createForm()
    {
        $this->collapse->childNodes->push(new Navbar\Form());

        return $this->form = $this->collapse->childNodes->last();
    }

    public function dark()
    {
        $this->attributes->removeAttributeClass('navbar-light');
        $this->attributes->addAttributeClass('navbar-dark');

        return $this->backgroundDark();
    }

    public function light()
    {
        $this->attributes->removeAttributeClass('navbar-dark');
        $this->attributes->addAttributeClass('navbar-light');

        return $this->backgroundLight();
    }

    public function render()
    {
        if ($this->nav->childNodes->count() || $this->textContent->count()) {
            return parent::render();
        }

        return '';
    }
}