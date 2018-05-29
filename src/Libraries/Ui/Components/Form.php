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

use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Form
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class Form extends Element
{
    public function __construct(array $attributes = [])
    {
        parent::__construct('form');

        if (isset($attributes[ 'id' ])) {
            $this->entity->setEntityName($attributes[ 'id' ]);
        }

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $this->attributes->addAttribute($name, $value);
            }
        }

        $this->attributes->addAttribute('role', 'form');
    }

    public function inline()
    {
        $this->attributes->addAttributeClass('form-inline');

        return $this;
    }

    public function horizontal()
    {
        $this->attributes->addAttributeClass('form-horizontal');

        return $this;
    }

    /**
     * @return Form\Group
     */
    public function createFormGroup()
    {
        $this->childNodes->push(new Form\Group());

        return $this->childNodes->last();
    }
}