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

namespace O2System\Framework\Libraries\Ui\Components\Form\Input;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components;
use O2System\Framework\Libraries\Ui\Components\Form;
use O2System\Framework\Libraries\Ui\Element;
use O2System\Framework\Libraries\Ui\Traits\Setters\SizingSetterTrait;
use O2System\Html\Element\Nodes;

/**
 * Class Group
 *
 * @package O2System\Framework\Libraries\Ui\Components\Buttons
 */
class Group extends Element
{
    use SizingSetterTrait;

    public $input;
    public $addOns;

    public function __construct()
    {
        parent::__construct('div');

        $this->attributes->addAttributeClass('input-group');
        $this->attributes->addAttribute('role', 'group');

        // Set input sizing class
        $this->setSizingClassPrefix('input-group');

        $this->addOns = new Nodes();
    }

    public function createInput(array $attributes = [])
    {
        $field = new Form\Elements\Input();

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $field->attributes->addAttribute($name, $value);

                if ($name === 'name') {
                    $this->entity->setEntityName('input-' . $value);

                    if ( ! array_key_exists('id', $attributes)) {
                        $field->attributes->setAttributeId('input-' . $value);
                    }
                }
            }
        }

        return $this->input = $field;
    }

    public function createSelect(array $options = [], $selected = null, array $attributes = [])
    {
        $field = new Form\Elements\Select();

        if (count($options)) {
            $field->createOptions($options, $selected);
        }

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $field->attributes->addAttribute($name, $value);

                if ($name === 'name') {
                    $this->entity->setEntityName('input-' . $value);

                    if ( ! array_key_exists('id', $attributes)) {
                        $field->attributes->setAttributeId('input-' . $value);
                    }
                }
            }
        }

        return $this->input = $field;
    }

    public function createAddon($node = null, $position = Group\AddOn::ADDON_LEFT)
    {
        $addOn = new Group\AddOn($position);

        if (isset($node)) {
            if ($node instanceof Element) {
                $addOn->childNodes->push($node);
            } else {
                $addOn->textContent->push($node);
            }
        }

        $this->addOns->push($addOn);

        return $this->addOns->last();
    }

    public function render()
    {
        $addOnsLeft = [];
        $addOnsRight = [];

        if ($this->addOns->count()) {
            foreach ($this->addOns as $addOn) {
                if ($addOn->position === Group\AddOn::ADDON_LEFT) {
                    $addOnsLeft[] = $addOn;
                } else {
                    $addOnsRight[] = $addOn;
                }
            }
        }

        $output[] = $this->open();

        // AddOn Left
        if (count($addOnsLeft)) {
            $prependContainer = new Element('div');
            $prependContainer->attributes->addAttributeClass('input-group-prepend');

            foreach ($addOnsLeft as $addOn) {
                $prependContainer->childNodes->push($addOn);
            }

            $output[] = $prependContainer;
        }

        // Input
        $output[] = $this->input;

        // AddOn Right
        if (count($addOnsRight)) {
            $appendContainer = new Element('div');
            $appendContainer->attributes->addAttributeClass('input-group-prepend');

            foreach ($addOnsRight as $addOn) {
                $appendContainer->childNodes->push($addOn);
            }

            $output[] = $appendContainer;
        }

        if ($this->hasChildNodes()) {
            foreach ($this->childNodes as $childNode) {
                if ($childNode instanceof Components\Dropdown) {
                    $childNode->attributes->removeAttributeClass('dropdown');
                    $childNode->attributes->addAttributeClass('input-group-btn');

                    $childNode->toggle->tagName = 'a';
                    $childNode->toggle->attributes->removeAttribute('type');
                }

                $output[] = $childNode;
            }
        }

        if ($this->hasTextContent()) {
            $output[] = implode(PHP_EOL, $this->textContent->getArrayCopy());
        }

        $output[] = $this->close();

        return implode(PHP_EOL, $output);
    }
}