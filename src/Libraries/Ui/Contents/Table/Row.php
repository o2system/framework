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
 * Class Row
 *
 * @package O2System\Framework\Libraries\Ui\Contents\Table
 */
class Row extends Element implements ContextualInterface
{
    use ContextualClassSetterTrait;

    public function __construct(array $attributes = [], $contextualClass = null)
    {
        parent::__construct('tr');

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

    public function createColumns(array $columns, $tagName = 'td')
    {
        foreach ($columns as $column) {
            if ($column instanceof Column) {
                $column->tagName = $tagName;
                $this->childNodes->push($column);
            } else {
                $columnElement = $this->createColumn($tagName);
                if ($column instanceof Element) {
                    $columnElement->entity->setEntityName($column->entity->getEntityName());
                    $columnElement->childNodes->push($column);
                } else {
                    $columnElement->entity->setEntityName('col-' . $column);
                    $columnElement->textContent->push($column);
                }
            }
        }

        return $this;
    }

    public function createColumn($tagName = 'td')
    {
        $column = new Element($tagName);
        $this->childNodes->push($column);

        return $this->childNodes->last();
    }
}