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
 * Class Label
 *
 * @package O2System\Framework\Libraries\Ui\Components\Form
 */
class Label extends Element
{
    public function __construct(array $attributes = [])
    {
        parent::__construct('label');

        if (isset($attributes[ 'id' ])) {
            $this->entity->setEntityName('label-' . $attributes[ 'id' ]);
        } elseif (isset($attributes[ 'for' ])) {
            $this->entity->setEntityName('label-' . $attributes[ 'name' ]);
        }

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $this->attributes->addAttribute($name, $value);
            }
        }

        $this->attributes->addAttributeClass('form-label-control');
    }

    public function screenReaderOnly($focusable = false)
    {
        $this->attributes->removeAttributeClass('form-label-control');

        return parent::screenReaderOnly($focusable);
    }

    public function render()
    {
        $output[] = $this->open();

        if ($this->hasChildNodes()) {
            $output[] = implode(PHP_EOL, $this->childNodes->getArrayCopy());
        }

        if ($this->hasTextContent()) {
            $output[] = implode('', $this->textContent->getArrayCopy());
        }

        $output[] = $this->close();

        return implode(PHP_EOL, $output);
    }
}