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

use O2System\Framework\Libraries\Ui\Contents\Icon;
use O2System\Framework\Libraries\Ui\Element;
use O2System\Framework\Libraries\Ui\Interfaces\ContextualInterface;
use O2System\Framework\Libraries\Ui\Traits\Setters\ContextualClassSetterTrait;
use O2System\Framework\Libraries\Ui\Traits\Setters\PopoverSetterTrait;
use O2System\Framework\Libraries\Ui\Traits\Setters\SizingSetterTrait;
use O2System\Framework\Libraries\Ui\Traits\Setters\TooltipSetterTrait;

/**
 * Class Button
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class Button extends Element implements ContextualInterface
{
    use ContextualClassSetterTrait;
    use SizingSetterTrait;
    use PopoverSetterTrait;
    use TooltipSetterTrait;

    public $icon;

    public function __construct($label = null, array $attributes = [], $contextualClass = 'default')
    {
        parent::__construct('button');
        $this->attributes->addAttribute('type', 'button');

        if (isset($attributes[ 'id' ])) {
            $this->entity->setEntityName('btn-' . $attributes[ 'id' ]);
        } elseif (isset($attributes[ 'name' ])) {
            $this->entity->setEntityName('btn-' . $attributes[ 'name' ]);
        }

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $this->attributes->addAttribute($name, $value);
            }
        }

        $this->attributes->addAttributeClass('btn');

        // Set button contextual class
        $this->setContextualClassPrefix('btn');
        $this->setContextualClassSuffix($contextualClass);

        $this->setSizingClassPrefix('btn');

        if (isset($label)) {
            $this->setLabel($label);
        }
    }

    public function setLabel($label)
    {
        $this->textContent->prepend($label);
        $this->entity->setEntityName($label);

        return $this;
    }

    public function setIcon($icon)
    {
        if ($icon instanceof Icon) {
            $this->icon = $icon;
        } else {
            $this->icon = new Icon($icon);
        }

        return $this;
    }

    public function disabled()
    {
        $this->attributes->addAttributeClass('disabled');

        return $this;
    }

    public function active()
    {
        $this->attributes->addAttributeClass('active');

        return $this;
    }

    public function render()
    {
        if ($this->icon instanceof Icon && $this->hasTextContent()) {
            $this->attributes->addAttributeClass('btn-icon');
        }

        $output[] = $this->open();

        if ($this->hasTextContent()) {
            $output[] = implode('', $this->textContent->getArrayCopy());
        }

        if ($this->hasChildNodes()) {
            $output[] = implode(PHP_EOL, $this->childNodes->getArrayCopy());
        }

        if ($this->icon instanceof Icon) {
            $output[] = $this->icon;
        }

        $output[] = $this->close();

        return implode(PHP_EOL, $output);
    }
}