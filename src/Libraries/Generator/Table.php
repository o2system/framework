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

namespace O2System\Framework\Libraries\Generator;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui;
use O2System\Framework\Models;
use O2System\Spl\Patterns\Structural\Composite\AbstractComposite;

/**
 * Class Table
 * @package O2System\Framework\Libraries\Generator
 */
class Table extends AbstractComposite
{
    /**
     * Table::$model
     *
     * @var Models\Sql\Model|Models\NoSql\Model|Models\Files\Model
     */
    protected $model;

    /**
     * Table::$columns
     *
     * @var array
     */
    protected $columns = [];

    // ------------------------------------------------------------------------

    /**
     * Table::$model
     *
     * @param Models\Sql\Model|Models\NoSql\Model|Models\Files\Model|string $model
     */
    public function setModel($model)
    {
        if($model instanceof Models\Sql\Model or $model instanceof Models\NoSql\Model or $model instanceof Models\Files\Model) {
            $this->model = $model;
        } elseif(class_exists($model)) {
            $this->model = models($model);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Table::setColumns
     *
     * @param array $columns
     *
     * @return static
     */
    public function setColumns(array $columns)
    {
        $defaultColumn = [
            'field'   => 'label',
            'label'   => 'Label',
            'attr'    => [
                'name'  => '',
                'id'    => '',
                'class' => '',
                'width' => '',
                'style' => '',
            ],
            'format'  => 'txt',
            'show'    => true,
            'sorting' => true,
            'hiding'  => true,
            'options' => false,
            'nested'  => false,
        ];

        $this->setPrependColumns();

        foreach ($columns as $key => $column) {
            $column = array_merge($defaultColumn, $column);
            $this->columns[ $key ] = $column;
        }

        $this->setAppendColumns();

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Table::setPrependColumns
     */
    protected function setPrependColumns()
    {
        $prependColumns = [
            'numbering' => [
                'field'   => 'numbering',
                'label'   => '#',
                'attr'    => [
                    'width' => '5%',
                ],
                'format'  => 'number',
                'show'    => true,
                'sorting' => true,
                'hiding'  => false,
                'options' => false,
                'nested'  => false,
                'content' => '',
            ],
            'id'        => [
                'field'   => 'id',
                'label'   => 'ID',
                'attr'    => [
                    'class' => 'text-right',
                    'width' => '3%',
                ],
                'format'  => 'txt',
                'show'    => true,
                'sorting' => true,
                'hiding'  => true,
                'options' => false,
                'nested'  => false,
            ],
            'checkbox'  => [
                'field'   => 'id',
                'label'   => new Ui\Components\Form\Elements\Checkbox([
                    'data-toggle' => 'table-crud-checkbox',
                ]),
                'attr'    => [
                    'width' => '2%',
                ],
                'format'  => 'checkbox',
                'show'    => true,
                'sorting' => false,
                'hiding'  => false,
                'options' => false,
                'nested'  => false,
            ],
            'images'    => [
                'field'   => 'images',
                'label'   => null,
                'attr'    => [
                    'width' => '5%',
                ],
                'format'  => 'image',
                'show'    => true,
                'sorting' => false,
                'hiding'  => true,
                'options' => false,
                'nested'  => false,
            ],
        ];

        foreach ($prependColumns as $key => $column) {
            if ($this->config[ $key ] === true) {
                $this->columns[ $key ] = $column;
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Table::setAppendColumns
     */
    protected function setAppendColumns()
    {
        $appendColumns = [
            'ordering'        => [
                'field'     => 'ordering',
                'label'     => '',
                'attr'      => [
                    'class' => 'width-fit',
                    'width' => '1%',
                ],
                'format'    => 'ordering',
                'show'      => true,
                'sorting'   => false,
                'filtering' => false,
                'hiding'    => false,
                'options'   => true,
                'nested'    => false,
            ],
            'status'          => [
                'field'    => 'record_status',
                'label'    => 'TABLE_LABEL_STATUS',
                'format'   => 'status',
                'show'     => false,
                'sorting'  => false,
                'hiding'   => true,
                'grouping' => true,
                'options'  => true,
                'nested'   => false,
            ],
            'createTimestamp' => [
                'field'    => 'record_create_timestamp',
                'label'    => 'TABLE_LABEL_CREATED_DATE',
                'format'   => 'date',
                'show'     => false,
                'hidden'   => true,
                'sorting'  => true,
                'hiding'   => true,
                'grouping' => false,
                'options'  => true,
                'nested'   => false,
            ],
            'updateTimestamp' => [
                'field'    => 'record_update_timestamp',
                'label'    => 'TABLE_LABEL_UPDATED_DATE',
                'format'   => 'date',
                'show'     => false,
                'hidden'   => true,
                'sorting'  => true,
                'grouping' => false,
                'hiding'   => true,
                'options'  => true,
                'nested'   => false,
            ],
            'actions'         => [
                'field'    => null,
                'label'    => null,
                'format'   => 'actions',
                'show'     => true,
                'sorting'  => false,
                'hiding'   => false,
                'grouping' => false,
                'options'  => false,
                'nested'   => false,
            ],
        ];

        foreach ($appendColumns as $key => $column) {
            if ($this->config[ $key ] === true) {
                $this->columns[ $key ] = $column;
            }
        }
    }

    // ------------------------------------------------------------------------

    protected function setColumnsFromModel()
    {
        $tableColumns = $this->model->db->getColumns($this->model->table);

        foreach($tableColumns as $tableColumn) {
            $label = $tableColumn['Field'];

            if($label === 'id') {
                $label = strtoupper($label);
            } else {
                $label = language(ucwords(readable($tableColumn['Field'])));
            }

            $columns[] = [
                'field'   => $tableColumn['Field'],
                'label'   => $label,
                'attr'    => [
                    'id'    => 'field-' . $tableColumn['Field']
                ],
                'format'  => $tableColumn->columns['Type'],
                'show'    => true,
                'sorting' => true,
                'hiding'  => false,
                'options' => false,
                'nested'  => false,
            ];
        }

        $this->setColumns($columns);
    }

    // ------------------------------------------------------------------------

    /**
     * Table::render
     *
     * @param array $options
     *
     * @return mixed
     */
    public function render(array $options = [])
    {
        if(empty($this->columns) and isset($this->model)) {
            $this->setColumnsFromModel();
        }
    }
}