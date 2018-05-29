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

use O2System\Framework\Libraries\Ui\Components\Form\Elements\Select\Traits\OptionCreateTrait;
use O2System\Framework\Libraries\Ui\Contents\Form;
use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Select
 * @package O2System\Framework\Libraries\Ui\Components\Form
 */
class Select extends Element
{
    use OptionCreateTrait;

    /**
     * Select::__construct
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct('select');

        if (isset($attributes[ 'id' ])) {
            $this->entity->setEntityName('select-' . $attributes[ 'id' ]);
        } elseif (isset($attributes[ 'name' ])) {
            $this->entity->setEntityName('select-' . $attributes[ 'name' ]);
        }

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $this->attributes->addAttribute($name, $value);
            }
        }

        $this->attributes->addAttributeClass(['form-control', 'custom-select']);
    }

    public function createGroup($label)
    {
        $group = new Select\Group();
        $group->textContent->push($label);

        $this->childNodes->push($group);

        return $this->childNodes->last();
    }

    public function multiple()
    {
        $this->attributes->addAttribute('multiple', 'multiple');

        return $this;
    }
}