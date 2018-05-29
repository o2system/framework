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

namespace O2System\Framework\Libraries\Ui\Components\Form\Elements\Traits;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components\Form;

/**
 * Trait ElementsCreatorTrait
 * @package O2System\Framework\Libraries\Ui\Components\Form\Traits
 */
trait ElementsCreatorTrait
{
    /**
     * Group::createLabel
     *
     * @param array $attributes
     *
     * @return \O2System\Framework\Libraries\Ui\Components\Form\Elements\Label
     */
    public function createLabel($textContent = null, array $attributes = [])
    {
        $label = new Form\Elements\Label($attributes);
        $label->attributes->addAttributeClass('col-form-label');

        if (isset($textContent)) {
            $label->textContent->push($textContent);
        }

        $this->childNodes->push($label);

        return $this->childNodes->last();
    }

    // ------------------------------------------------------------------------

    /**
     * Group::createInput
     *
     * @param array $attributes
     *
     * @return \O2System\Framework\Libraries\Ui\Components\Form\Elements\Input
     */
    public function createInput(array $attributes = [])
    {
        if (isset($attributes[ 'type' ])) {
            switch ($attributes[ 'type' ]) {
                default:
                    $input = new Form\Elements\Input($attributes);
                    break;
                case 'checkbox':
                    $input = new Form\Elements\Checkbox($attributes);
                    break;
                case 'radio':
                    $input = new Form\Elements\Radio($attributes);
                    break;
            }
        } else {
            $input = new Form\Elements\Input($attributes);
        }

        $this->childNodes->push($input);

        return $this->childNodes->last();
    }

    // ------------------------------------------------------------------------

    /**
     * Group::createTextarea
     *
     * @param array $attributes
     *
     * @return \O2System\Framework\Libraries\Ui\Components\Form\Elements\Textarea
     */
    public function createTextarea(array $attributes = [])
    {
        $this->childNodes->push(new Form\Elements\Textarea($attributes));

        return $this->childNodes->last();
    }

    // ------------------------------------------------------------------------

    /**
     * Group::createSelect
     *
     * @param array $options
     * @param null  $selected
     * @param array $attributes
     *
     * @return \O2System\Framework\Libraries\Ui\Components\Form\Elements\Select
     */
    public function createSelect(array $options = [], $selected = null, array $attributes = [])
    {
        $select = new Form\Elements\Select($attributes);

        if (count($options)) {
            $select->createOptions($options, $selected);
        }

        $this->childNodes->push($select);

        return $this->childNodes->last();
    }

    // ------------------------------------------------------------------------

    /**
     * @param array $attributes
     *
     * @return \O2System\Framework\Libraries\Ui\Components\Form\Input\Group
     */
    public function createInputGroup(array $attributes = [])
    {
        $inputGroup = new Form\Input\Group();

        if (count($attributes)) {
            $inputGroup->createInput($attributes);
        }

        $this->childNodes->push($inputGroup);

        return $this->input = $this->childNodes->last();
    }

    public function createButton($label, array $attributes = [])
    {
        $button = new Form\Elements\Button($label);

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $button->attributes->addAttribute($name, $value);

                if ($name === 'name') {
                    $this->entity->setEntityName('btn-' . $value);

                    if ( ! array_key_exists('id', $attributes)) {
                        $button->attributes->setAttributeId('btn-' . $value);
                    }
                }
            }
        }

        $this->childNodes->push($button);

        return $this->childNodes->last();
    }
}