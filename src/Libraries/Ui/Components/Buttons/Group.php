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

use O2System\Framework\Libraries\Ui\Components\Button;
use O2System\Framework\Libraries\Ui\Components\Dropdown;
use O2System\Framework\Libraries\Ui\Element;
use O2System\Framework\Libraries\Ui\Traits\Setters\SizingSetterTrait;

/**
 * Class Group
 *
 * @package O2System\Framework\Libraries\Ui\Components\Buttons
 */
class Group extends Element
{
    use SizingSetterTrait;

    public function __construct()
    {
        parent::__construct('div');

        $this->attributes->addAttributeClass('btn-group');
        $this->attributes->addAttribute('role', 'group');

        // Set button sizing class
        $this->setSizingClassPrefix('btn-group');
    }

    /**
     * @param $label
     *
     * @return Button
     */
    public function createButton($label)
    {
        $node = new Button();

        if ($label instanceof Button) {
            $node = $label;
        } elseif ($label instanceof Dropdown) {
            $node = clone $label;
            $node->attributes->removeAttributeClass('dropdown');
            $node->attributes->addAttributeClass('btn-group');
            $node->attributes->addAttribute('role', 'group');

            $node->childNodes->push($label->toggle);
            $node->childNodes->push($label->menu);
        } else {
            $node->setLabel($label);
            if (is_numeric($label)) {
                $node->entity->setEntityName('button-' . $label);
            } else {
                $node->entity->setEntityName($label);
            }
        }

        $this->childNodes->push($node);

        return $this->childNodes->last();
    }

    public function verticalStacked()
    {
        $this->attributes->removeAttributeClass('btn-group');
        $this->attributes->addAttributeClass('btn-group-vertical');

        return $this;
    }

    public function justified()
    {
        $this->attributes->addAttributeClass('btn-group-justified');

        return $this;
    }
}