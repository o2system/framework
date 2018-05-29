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

use O2System\Framework\Libraries\Ui\Contents\Form;
use O2System\Framework\Libraries\Ui\Element;
use O2System\Framework\Libraries\Ui\Traits\Setters\SizingSetterTrait;

/**
 * Class Textarea
 *
 * @package O2System\Framework\Libraries\Ui\Components\Form
 */
class Textarea extends Element
{
    use SizingSetterTrait;

    public function __construct(array $attributes = [])
    {
        parent::__construct('textarea');

        if (isset($attributes[ 'id' ])) {
            $this->entity->setEntityName('textarea-' . $attributes[ 'id' ]);
        } elseif (isset($attributes[ 'name' ])) {
            $this->entity->setEntityName('textarea-' . $attributes[ 'name' ]);
        }

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $this->attributes->addAttribute($name, $value);
            }
        }

        $this->attributes->addAttributeClass('form-control');

        $this->setSizingClassPrefix('form-control');
    }

    public function disabled()
    {
        $this->attributes->addAttribute('disabled', 'disabled');

        return $this;
    }

    public function readOnly()
    {
        $this->attributes->addAttribute('readonly', 'readonly');

        return $this;
    }
}