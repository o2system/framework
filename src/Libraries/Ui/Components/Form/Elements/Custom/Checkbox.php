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

namespace O2System\Framework\Libraries\Ui\Components\Form\Elements;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Checkbox
 *
 * @package O2System\Framework\Libraries\Ui\Components\Form
 */
class Checkbox extends Element
{
    public $input;

    public function __construct($label = null, array $attributes = [])
    {
        parent::__construct('label');

        $this->attributes->addAttributeClass(['custom-control', 'custom-checkbox']);

        $attributes[ 'type' ] = 'checkbox';

        $checkbox = new Input();
        $checkbox->attributes->removeAttributeClass('form-control');
        $checkbox->attributes->addAttributeClass('custom-control-input');

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $checkbox->attributes->addAttribute($name, $value);

                if ($name === 'name') {
                    $this->entity->setEntityName('input-' . $value);
                    $label->entity->setEntityName('label-' . $value);
                    $label->attributes->addAttribute('for', $value);

                    if ( ! array_key_exists('id', $attributes)) {
                        $checkbox->attributes->setAttributeId('input-' . $value);
                    }
                }
            }
        }

        $this->input = $this->childNodes->push($checkbox);

        $indicator = new Element('span');
        $indicator->attributes->addAttributeClass('custom-control-indicator');
        $this->childNodes->push($indicator);

        if (isset($label)) {
            $description = new Element('span');
            $description->attributes->addAttributeClass('custom-control-description');
            $description->textContent->push(trim($label));
            $this->childNodes->push($indicator);
        }
    }
}