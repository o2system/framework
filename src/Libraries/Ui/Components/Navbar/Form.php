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

use O2System\Framework\Libraries\Ui\Components;

/**
 * Class Form
 *
 * @package O2System\Framework\Libraries\Ui\Components\Navbar
 */
class Form extends Components\Form
{
    public function __construct()
    {
        parent::__construct();
        $this->attributes->addAttributeClass(['form-inline', 'my-2', 'my-lg-0']);
    }

    /**
     * @param array $attributes
     *
     * @return \O2System\Framework\Libraries\Ui\Components\Form\Elements\Input
     */
    public function createInput(array $attributes = [])
    {
        $field = new Components\Form\Elements\Input();

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

        $this->childNodes->push($field);

        return $this->childNodes->last();
    }

    public function createButton($label, array $attributes = [])
    {
        $button = new Components\Form\Elements\Button($label);

        if ( ! array_key_exists('class', $attributes)) {
            $button->attributes->addAttributeClass(['btn', 'my-2', 'my-sm-0']);
        }

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $button->attributes->addAttribute($name, $value);
            }
        }

        $this->childNodes->push($button);

        return $this->childNodes->last();
    }
}