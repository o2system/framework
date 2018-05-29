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

namespace O2System\Framework\Libraries\Ui\Components\Buttons;

use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Toolbar
 *
 * @package O2System\Framework\Libraries\Ui\Components\Buttons
 */
class Toolbar extends Element
{
    public function __construct()
    {
        parent::__construct('div');

        $this->attributes->addAttributeClass('btn-toolbar');
        $this->attributes->addAttribute('role', 'toolbar');
    }

    public function createButtonGroup()
    {
        $node = new Group();
        $this->childNodes->push($node);

        return $this->childNodes->last();
    }

    public function createDropdownButtonGroup($label)
    {
        if ($label instanceof Dropdown) {
            $node = clone $label;
        } else {
            $node = new Dropdown($label);
        }

        $this->childNodes->push($node);

        return $this->childNodes->last();
    }

    /**
     * Toolbar::createInputGroup
     *
     * @return \O2System\Framework\Libraries\Ui\Components\Form\Input\Group
     */
    public function createInputGroup()
    {
        $node = new \O2System\Framework\Libraries\Ui\Components\Form\Input\Group();
        $this->childNodes->push($node);

        return $this->childNodes->last();

    }
}