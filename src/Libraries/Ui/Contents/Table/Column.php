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

namespace O2System\Framework\Libraries\Ui\Contents\Table;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;
use O2System\Framework\Libraries\Ui\Interfaces\ContextualInterface;
use O2System\Framework\Libraries\Ui\Traits\Setters\ContextualClassSetterTrait;

/**
 * Class Column
 *
 * @package O2System\Framework\Libraries\Ui\Contents\Table
 */
class Column extends Element implements ContextualInterface
{
    use ContextualClassSetterTrait;

    public function __construct(array $attributes = [], $contextualClass = null)
    {
        parent::__construct('td');

        if (isset($attributes[ 'id' ])) {
            $this->entity->setEntityName($attributes[ 'id' ]);
        }

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $this->attributes->addAttribute($name, $value);
            }
        }

        $this->setContextualClassPrefix('table');

        if (isset($contextualClass)) {
            $this->setContextualClass($contextualClass);
        }
    }
}