<?php
/**
 * This file is part of the O2System Framework package.
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

/**
 * Class Row
 *
 * @package O2System\Framework\Libraries\Ui\Grid
 */
class Row extends Element
{
    /**
     * Row::$auto
     *
     * @var bool
     */
    public $auto = false;

    /**
     * Row::$childNodesEntities
     *
     * @var array
     */
    protected $childNodesEntities = [];

    // ------------------------------------------------------------------------

    /**
     * Row::__construct
     */
    public function __construct()
    {
        parent::__construct('div');
        $this->attributes->addAttributeClass('row');
    }

    // ------------------------------------------------------------------------

    /**
     * Row::getColumn
     *
     * @param string $index
     *
     * @return \O2System\Framework\Libraries\Ui\Contents\Table\Column
     */
    public function getColumn($index)
    {
        if (is_string($index) and in_array($index, $this->childNodesEntities)) {
            if (false !== ($key = array_search($index, $this->childNodesEntities))) {
                if ($this->childNodes->offsetExists($key)) {
                    $index = $key;
                }
            }
        }

        return $this->childNodes->offsetGet($index);
    }

    // ------------------------------------------------------------------------

    /**
     * Row::createoColumn
     *
     * @param array $attributes
     *
     * @return \O2System\Framework\Libraries\Ui\Contents\Table\Column
     */
    public function createColumn(array $attributes = [])
    {
        $this->addColumn(new Column($attributes));

        return $this->childNodes->last();
    }

    // ------------------------------------------------------------------------

    /**
     * Row::addColumn
     *
     * @param Column $column
     *
     * @return static
     */
    public function addColumn($column)
    {
        if ($column instanceof Element) {
            if ($column->entity->getEntityName() === '') {
                $column->entity->setEntityName('col-' . ($this->childNodes->count() + 1));
            }

            $this->pushColumnChildNodes($column);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Row::pushColumnChildNodes
     *
     * @param \O2System\Framework\Libraries\Ui\Element $column
     */
    protected function pushColumnChildNodes(Element $column)
    {
        if ( ! $this->hasItem($column->entity->getEntityName())) {
            $this->childNodes[] = $column;
            $this->childNodes->last();
            $this->childNodesEntities[ $this->childNodes->key() ] = $column->entity->getEntityName();
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Row::hasItem
     *
     * @param string $index
     *
     * @return bool
     */
    public function hasItem($index)
    {
        if (is_string($index) and in_array($index, $this->childNodesEntities)) {
            if (false !== ($key = array_search($index, $this->childNodesEntities))) {
                if ($this->childNodes->offsetExists($key)) {
                    return true;
                }
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Row::render
     *
     * @return string
     */
    public function render()
    {
        $output[] = $this->open();

        if ( ! empty($this->content)) {
            $output[] = implode(PHP_EOL, $this->content);
        }

        if ($this->hasChildNodes()) {

            if ($this->auto) {
                $columns = $this->childNodes->count();

                if ($columns > 2) {
                    $extraSmallColumn = @round(12 / ($columns - 2));
                    $smallColumn = @round(12 / ($columns - 1));
                }

                $mediumColumn = round(12 / ($columns));
                $largeColumn = round(12 / ($columns));
            }

            foreach ($this->childNodes as $childNode) {
                if ($this->auto) {
                    if (isset($extraSmallColumn)) {
                        $childNode->attributes->addAttributeClass('col-xs-' . $extraSmallColumn);
                    }

                    if (isset($smallColumn)) {
                        $childNode->attributes->addAttributeClass('col-sm-' . $smallColumn);
                    }

                    $childNode->attributes->addAttributeClass('col-md-' . $mediumColumn);
                    $childNode->attributes->addAttributeClass('col-lg-' . $largeColumn);
                }

                $output[] = $childNode->render() . PHP_EOL;
            }
        }

        $output[] = $this->close();

        return implode(PHP_EOL, $output);
    }
}