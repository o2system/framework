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

namespace O2System\Framework\Libraries\Ui\Grid;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;
use O2System\Framework\Libraries\Ui\Traits\Setters\SizingSetterTrait;

/**
 * Class Column
 * @package O2System\Framework\Libraries\Ui\Grid
 */
class Column extends Element
{
    use SizingSetterTrait;

    public function __construct(array $attributes = [])
    {
        parent::__construct('div');

        if (isset($attributes[ 'id' ])) {
            $this->entity->setEntityName($attributes[ 'id' ]);
        }

        $this->setSizingClassPrefix('col');

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $this->attributes->addAttribute($name, $value);
            }
        }
    }

    public function render()
    {
        if ($this->attributes->hasAttribute('class') === false) {
            $this->attributes->addAttributeClass('col');
        }

        return parent::render();
    }
}