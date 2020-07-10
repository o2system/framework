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
use O2System\Html\Element;
use O2System\Spl\Patterns\Structural\Composite\AbstractComposite;
use O2System\Spl\Traits\Collectors\ConfigCollectorTrait;

/**
 * Class Datatable
 * @package O2System\Framework\Libraries\Generator
 */
class Datatable extends AbstractComposite
{
    use ConfigCollectorTrait;

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

    /**
     * Table::$view
     *
     * @var string
     */
    protected $view;

    /**
     * Table::$vars
     *
     * @var array
     */
    protected $vars;

    // ------------------------------------------------------------------------

    /**
     * Datatable::__construct
     */
    public function __construct()
    {
        language()->loadFile('generator/table');

        // Set default config
        $this->config['paging'] = false;
    }

    // ------------------------------------------------------------------------

    /**
     * Table::$model
     *
     * @param Models\Sql\Model|Models\NoSql\Model|Models\Files\Model|string $model
     *
     * @return static
     */
    public function setModel($model)
    {
        if ($model instanceof Models\Sql\Model or $model instanceof Models\NoSql\Model or $model instanceof Models\Files\Model) {
            $this->model = $model;
        } elseif (class_exists($model)) {
            $this->model = models($model);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Table::setView
     *
     * @param $view
     * @param array $vars
     * @return static
     */
    public function setView($view, array $vars = [])
    {
        $this->view = $view;
        $this->vars = $vars;

        return $this;
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
            'field' => 'label',
            'label' => 'Label',
            'attr' => [
                'name' => '',
                'id' => '',
                'class' => '',
                'width' => '',
                'style' => '',
            ],
            'format' => 'txt',
            'link' => null,
            'visible' => true,
            'searchable' => false,
            'orderable' => false,
            'options' => false,
            'nested' => false,
        ];

        $this->setPrependColumns();

        foreach ($columns as $key => $column) {
            $column = array_merge($defaultColumn, $column);
            $this->columns[$key] = $column;
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
                'field' => 'numbering',
                'label' => '#',
                'attr' => [
                    'width' => '5%',
                ],
                'format' => 'numbering',
                'link' => null,
                'visible' => true,
                'searchable' => false,
                'orderable' => true,
                'options' => false,
                'nested' => false,
            ],
            'id' => [
                'field' => 'id',
                'label' => 'ID',
                'attr' => [
                    'class' => 'text-right',
                    'width' => '3%',
                ],
                'format' => 'txt',
                'link' => null,
                'visible' => true,
                'searchable' => false,
                'orderable' => true,
                'options' => false,
                'nested' => false,
            ],
            'checkbox' => [
                'field' => 'id',
                'label' => new Ui\Components\Form\Elements\Checkbox([
                    'data-toggle' => 'table-crud-checkbox',
                ]),
                'attr' => [
                    'width' => '2%',
                ],
                'format' => 'checkbox',
                'link' => null,
                'visible' => true,
                'searchable' => false,
                'orderable' => true,
                'options' => false,
                'nested' => false,
            ],
            'images' => [
                'field' => 'images',
                'label' => null,
                'attr' => [
                    'width' => '5%',
                ],
                'format' => 'image',
                'link' => null,
                'visible' => true,
                'searchable' => false,
                'orderable' => false,
                'options' => false,
                'nested' => false,
            ],
        ];

        foreach ($prependColumns as $key => $column) {
            if ($this->config[$key] === true) {
                $this->columns[$key] = $column;
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
            'ordering' => [
                'field' => 'ordering',
                'label' => '',
                'attr' => [
                    'class' => 'width-fit',
                    'width' => '1%',
                ],
                'format' => 'ordering',
                'link' => null,
                'visible' => false,
                'searchable' => false,
                'orderable' => false,
                'options' => false,
                'nested' => false,
            ],
            'status' => [
                'field' => 'record_status',
                'label' => language('TABLE_LABEL_STATUS'),
                'format' => 'status',
                'link' => null,
                'visible' => true,
                'searchable' => false,
                'orderable' => false,
                'options' => false,
                'nested' => false,
            ],
            'createTimestamp' => [
                'field' => 'record_create_timestamp',
                'label' => language('TABLE_LABEL_CREATE_TIMESTAMP'),
                'format' => 'date',
                'link' => null,
                'visible' => true,
                'searchable' => false,
                'orderable' => true,
                'options' => false,
                'nested' => false,
            ],
            'updateTimestamp' => [
                'field' => 'record_update_timestamp',
                'label' => language('TABLE_LABEL_UPDATE_TIMESTAMP'),
                'format' => 'date',
                'link' => null,
                'visible' => true,
                'searchable' => false,
                'orderable' => true,
                'options' => false,
                'nested' => false,
            ],
            'actions' => [
                'field' => null,
                'label' => null,
                'format' => 'actions',
                'link' => null,
                'visible' => true,
                'searchable' => false,
                'orderable' => true,
                'options' => false,
                'nested' => false,
            ],
        ];

        foreach ($appendColumns as $key => $column) {
            if ($this->config[$key] === true) {
                $this->columns[$key] = $column;
            }
        }
    }

    // ------------------------------------------------------------------------

    protected function setColumnsFromModel()
    {
        $tableColumns = $this->model->db->getColumns($this->model->table);

        foreach ($tableColumns as $tableColumn) {
            $label = $tableColumn['Field'];

            if ($label === 'id') {
                $label = strtoupper($label);
            } else {
                $label = language(ucwords(readable(dash($tableColumn['Field']))));
            }

            $columns[] = [
                'field' => $tableColumn['Field'],
                'label' => $label,
                'attr' => [
                    'id' => 'field-' . dash($tableColumn['Field'])
                ],
                'format' => $tableColumn->columns['Type'],
                'link' => null,
                'visible' => true,
                'searchable' => true,
                'orderable' => true,
                'options' => false,
                'nested' => false,
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
        if (empty($this->columns) and isset($this->model)) {
            $this->setColumnsFromModel();
        }

        $table = new Ui\Contents\Table();
        $table->attributes->addAttributeClass('datatable');
        $tr = $table->header->createRow();

        $datatableConfig = null;

        $columnNumber = 0;
        foreach ($this->columns as $column) {
            $th = $tr->createColumn('th');

            if (!empty($column['attr'])) {
                foreach ($column['attr'] as $attrName => $attrValue) {
                    $th->attributes->addAttribute($attrName, $attrValue);
                }
            }

            $th->textContent->push($column['label']);

            $datatableConfig['columns'][$columnNumber] = [
                'target' => $columnNumber,
                'orderable' => ($column['orderable'] === true ? 'true' : 'false'),
                'visible' => ($column['visible'] === true ? 'true' : 'false'),
                'searchable' => ($column['searchable'] === true ? 'true' : 'false'),
            ];

            $columnNumber++;
        }

        if ($this->config['paging'] === false) {
            $result = $this->model->all();
        } elseif (is_numeric($this->config['paging'])) {
            $result = $this->model->allWithPaging(null, $this->config['paging']);
        }

        $info = $result->getInfo();

        if ($result->count()) {
            foreach ($result as $row) {
                $tr = $table->body->createRow();

                foreach ($this->columns as $column) {
                    $td = $tr->createColumn('td');
                    $content = $row->offsetGet($column['field']);

                    if(isset($column['link'])) {
                        if($column['link'] === 'edit') {
                            $column['link'] = current_url('edit/' . $row->id);
                        }
                    }

                    switch ($column['format']) {
                        default:
                            if(isset($column['link'])) {
                                $td->childNodes->push(new Ui\Contents\Link($content, $column['link']));
                            } else {
                                $td->textContent->push($content);
                            }
                            break;
                        case 'numbering':
                            if(isset($column['link'])) {
                                $td->childNodes->push(new Ui\Contents\Link($info->numbering->start++, $column['link']));
                            } else {
                                $td->textContent->push($info->numbering->start++);
                            }
                        case 'badge':
                            $badge = new Ui\Components\Badge($content);

                            if(isset($column['link'])) {
                                $td->childNodes->push(new Ui\Contents\Link($badge, $column['link']));
                            } else {
                                $td->childNodes->push($badge);
                            }

                            unset($badge);
                            break;
                        case 'status':
                            if ($content === 'PUBLISH') {
                                $contextualClass = Ui\Components\Badge::SUCCESS_CONTEXT;
                            } elseif ($content === 'UNPUBLISH') {
                                $contextualClass = Ui\Components\Badge::WARNING_CONTEXT;
                            } elseif ($content === 'DELETE') {
                                $contextualClass = Ui\Components\Badge::DANGER_CONTEXT;
                            } elseif ($content === 'ARCHIVE') {
                                $contextualClass = Ui\Components\Badge::INFO_CONTEXT;
                            } elseif ($content === 'DRAFT') {
                                $contextualClass = Ui\Components\Badge::DEFAULT_CONTEXT;
                            }

                            $badge = new Ui\Components\Badge($content, $contextualClass);

                            if(isset($column['link'])) {
                                $td->childNodes->push(new Ui\Contents\Link($badge, $column['link']));
                            } else {
                                $td->childNodes->push($badge);
                            }

                            unset($badge);
                            break;
                    }
                }
            }
        }

        $script = new Element('script');
        $script->textContent->push("
            $(document).ready(function() {
                $('.datatable').DataTable(". (empty($datatableConfig) ? null : str_replace(['"true"', '"false"'],['true', 'false'], json_encode($datatableConfig))) .");
            });
        ");

        $html = implode(PHP_EOL, [
            $table->render(),
            $script->render()
        ]);

        if (isset($options['return']) and $options['return'] === true) {
            if (isset($this->view)) {
                $this->vars['table'] = $html;

                return view($this->view, $this->vars, true);
            }
        } elseif (isset($this->view)) {
            $this->vars['table'] = $html;

            view($this->view, $this->vars);

            return '';
        }

        return $html;
    }
}