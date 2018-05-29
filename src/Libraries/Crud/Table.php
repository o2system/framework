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

namespace O2System\Framework\Libraries\Crud;

// ------------------------------------------------------------------------

use O2System\Database\DataObjects\Result;
use O2System\Framework\Libraries\Ui;
use O2System\Framework\Models;
use O2System\Html\Element;

/**
 * Class Table
 *
 * @package O2System\Libraries\CRUD
 */
class Table extends Ui\Contents\Table
{
    /**
     * Table::$rows
     *
     * Table rows resource object.
     *
     * @var \O2System\Database\DataObjects\Result
     */
    public $rows;
    /**
     * Table::$columns
     *
     * @var array
     */
    public $columns = [];
    /**
     * Table::$config
     *
     * Table configuration.
     *
     * @var array
     */
    protected $config = [
        'id'              => true,
        'numbering'       => true,
        'checkbox'        => true,
        'images'          => false,
        'actions'         => true,
        'labels'          => false,
        'responsive'      => true,
        'nested'          => false,
        'ordering'        => false,
        'createTimestamp' => false,
        'updateTimestamp' => false,
        'status'          => true,
        'heading'         => null,
        'entries'         => [
            'minimum' => 10,
            'maximum' => 100,
            'step'    => 10,
        ],
    ];
    protected $tools = [
        'create'    => [
            'label'      => true,
            'show'       => true,
            'contextual' => 'default',
            'icon'       => 'fa fa-file-o',
            'href'       => false,
        ],
        'update'    => [
            'label'      => true,
            'show'       => true,
            'contextual' => 'default',
            'icon'       => 'fa fa-pencil',
            'href'       => false,
        ],
        'publish'   => [
            'label'      => true,
            'show'       => true,
            'contextual' => 'default',
            'icon'       => 'fa fa-eye',
            'href'       => false,
        ],
        'unpublish' => [
            'label'      => true,
            'show'       => true,
            'contextual' => 'default',
            'icon'       => 'fa fa-eye-slash',
            'href'       => false,
        ],
        'delete'    => [
            'label'      => true,
            'show'       => true,
            'contextual' => 'default',
            'icon'       => 'fa fa-trash',
            'href'       => false,
        ],
        'archive'   => [
            'label'      => true,
            'show'       => true,
            'contextual' => 'default',
            'icon'       => 'fa fa-archive',
            'href'       => false,
        ],
    ];
    protected $options = [
        'grid'    => [
            'label'      => true,
            'show'       => true,
            'contextual' => 'default',
            'icon'       => 'fa fa-id-card',
            'href'       => false,
        ],
        'reset'   => [
            'label'      => false,
            'show'       => true,
            'contextual' => 'default',
            'icon'       => 'fa fa-repeat',
            'href'       => false,
        ],
        'reload'  => [
            'label'      => false,
            'show'       => true,
            'contextual' => 'default',
            'icon'       => 'fa fa-refresh',
            'href'       => false,
        ],
        'archive' => [
            'label'      => true,
            'show'       => true,
            'contextual' => 'success',
            'icon'       => 'fa fa-archive',
            'href'       => false,
        ],
        'help'    => [
            'label'      => true,
            'show'       => true,
            'contextual' => 'default',
            'icon'       => 'fa fa-help',
            'href'       => false,
        ],
    ];
    protected $actions = [
        'view'    => [
            'label'      => false,
            'show'       => false,
            'contextual' => 'default',
            'icon'       => 'fa fa-eye',
            'href'       => false,
        ],
        'copy'    => [
            'label'      => false,
            'show'       => false,
            'contextual' => 'default',
            'icon'       => 'fa fa-clone',
            'href'       => false,
        ],
        'edit'    => [
            'label'      => false,
            'show'       => false,
            'contextual' => 'default',
            'icon'       => 'fa fa-pencil',
            'href'       => false,
        ],
        'delete'  => [
            'label'      => false,
            'show'       => false,
            'contextual' => 'default',
            'icon'       => 'fa fa-trash',
            'href'       => false,
        ],
        'archive' => [
            'label'      => false,
            'show'       => false,
            'contextual' => 'default',
            'icon'       => 'fa fa-archive',
            'href'       => false,
        ],
        'export'  => [
            'label'      => false,
            'show'       => false,
            'contextual' => 'default',
            'icon'       => 'fa fa-mail-forward',
            'href'       => false,
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Table::__construct
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct();

        language()->loadFile('crud/table');
        presenter()->assets->loadJs('crud/table');

        $this->attributes->addAttributeClass(['table-striped', 'table-hover', 'mb-0']);
        $this->setConfig($config);
    }

    // ------------------------------------------------------------------------

    /**
     * Table::setConfig
     *
     * Set table configuration.
     *
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = array_merge($this->config, $config);

        if ($this->config[ 'responsive' ]) {
            $this->responsive();
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    public function setRows(Result $rows)
    {
        $this->rows = $rows;

        return $this;
    }

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

    public function setActions(array $actions)
    {
        $this->actions = array_merge($this->actions, $actions);

        return $this;
    }

    public function setTools(array $tools)
    {
        $this->tools = array_merge($this->tools, $tools);

        return $this;
    }

    public function setOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    public function render()
    {
        if ($this->config[ 'ordering' ]) {
            $this->attributes->addAttributeClass('table-ordering');
        }

        // Render header
        $tr = $this->header->createRow();

        foreach ($this->columns as $key => $column) {

            $th = $tr->createColumn('th');

            $column[ 'attr' ][ 'data-column' ] = $key;

            foreach ($column[ 'attr' ] as $name => $value) {
                if ( ! empty($value)) {
                    $th->attributes->addAttribute($name, $value);
                }
            }

            if ($column[ 'sorting' ] === true) {
                $icon = new Ui\Contents\Icon('fa fa-sort');
                $icon->attributes->addAttributeClass(['text-muted', 'float-right', 'mt-1']);

                $th->attributes->addAttributeClass('col-sortable');
                $th->attributes->addAttribute('data-toggle', 'sort');
                $th->childNodes->push($icon);
            }

            if ($column[ 'show' ] === false) {
                $th->attributes->addAttributeClass('hidden');
            }

            $th->textContent->push(language()->getLine($column[ 'label' ]));
        }

        // Render tbody
        if ($this->rows->count() > 0) {
            $totalEntries = $this->rows->count();
            $totalRows = $this->rows->countAll();
            $currentPage = input()->get('page') ? input()->get('page') : 1;
            $startNumber = $currentPage == 1 ? 1 : $currentPage * $this->config[ 'entries' ][ 'minimum' ];
            $totalPages = round($totalRows / $this->config[ 'entries' ][ 'minimum' ]);
            $totalPages = $totalPages == 0 && $totalRows > 0 ? 1 : $totalPages;

            $i = $startNumber;
            foreach ($this->rows as $row) {
                $row->numbering = $i;
                $tr = $this->body->createRow();
                $tr->attributes->addAttribute('data-id', $row->id);

                foreach ($this->columns as $key => $column) {

                    $column[ 'key' ] = $key;
                    $td = $tr->createColumn();

                    $column[ 'attr' ][ 'data-column' ] = $key;

                    foreach ($column[ 'attr' ] as $name => $value) {
                        if ( ! empty($value)) {
                            $td->attributes->addAttribute($name, $value);
                        }
                    }

                    if ($column[ 'show' ] === false) {
                        $td->attributes->addAttributeClass('hidden');
                    }

                    $td->attributes->addAttributeClass('col-body-' . $key);

                    $this->renderBodyColumn($td, $column, $row);
                }

                $i++;
            }
        } else {
            $totalEntries = 0;
            $totalRows = 0;
            $currentPage = 1;
            $startNumber = 0;
            $totalPages = 0;
        }

        $showEntries = input()->get('entries') ? input()->get('entries') : $this->config[ 'entries' ][ 'minimum' ];

        // Build table card
        $card = new Ui\Components\Card();
        $card->attributes->addAttribute('data-role', 'table-crud');
        $card->attributes->addAttributeClass('card-table');

        // Build table header tools
        $tools = new Element('div', 'cardHeaderTools');
        $tools->attributes->addAttributeClass(['card-tools', 'float-left']);

        $buttons = new Ui\Components\Buttons\Group();
        foreach ($this->tools as $key => $tool) {
            if ($tool[ 'show' ] === false) {
                continue;
            }

            if ($tool[ 'label' ] === true) {
                $button = $buttons->createButton(language()->getLine('TABLE_BUTTON_' . strtoupper($key)));
                if ( ! empty($tool[ 'icon' ])) {
                    $button->setIcon($tool[ 'icon' ]);
                }
            } else {
                $button = $buttons->createButton(new Ui\Contents\Icon($tool[ 'icon' ]));
                $button->attributes->addAttribute('data-toggle', 'tooltip');
                $button->attributes->addAttribute('title',
                    language()->getLine('TABLE_BUTTON_' . strtoupper($key)));
            }

            if ( ! empty($tool[ 'href' ])) {
                $tool[ 'data-url' ] = $tool[ 'href' ];
            }

            if (isset($tool[ 'attr' ])) {
                foreach ($tool[ 'attr' ] as $name => $value) {
                    $button->attributes->addAttribute($name, $value);
                }
            }

            $button->setContextualClass($tool[ 'contextual' ]);
            $button->smallSize();
            $button->attributes->addAttribute('data-action', $key);
        }

        $tools->childNodes->push($buttons);

        $card->header->childNodes->push($tools);

        // Build table header options
        $options = new Element('div', 'cardHeaderOptions');
        $options->attributes->addAttributeClass(['card-options', 'float-right']);

        $buttons = new Ui\Components\Buttons\Group();
        foreach ($this->options as $key => $option) {
            if ($option[ 'show' ] === false) {
                continue;
            }

            if ($option[ 'label' ] === true) {
                $button = $buttons->createButton(language()->getLine('TABLE_BUTTON_' . strtoupper($key)));
                if ( ! empty($option[ 'icon' ])) {
                    $button->setIcon($option[ 'icon' ]);
                }
            } else {
                $button = $buttons->createButton(new Ui\Contents\Icon($option[ 'icon' ]));
                $button->attributes->addAttribute('data-toggle', 'tooltip');
                $button->attributes->addAttribute('title',
                    language()->getLine('TABLE_BUTTON_' . strtoupper($key)));
            }

            if ( ! empty($option[ 'href' ])) {
                $option[ 'data-url' ] = $option[ 'href' ];
            }

            if (isset($option[ 'attr' ])) {
                foreach ($option[ 'attr' ] as $name => $value) {
                    $button->attributes->addAttribute($name, $value);
                }
            }

            $button->setContextualClass($option[ 'contextual' ]);
            $button->smallSize();
            $button->attributes->addAttribute('data-action', $key);
        }

        $options->childNodes->push($buttons);

        $card->header->childNodes->push($options);

        // Build table body
        $cardBody = $card->createBody();
        $cardBodyRow = $cardBody->createRow();

        $columnSearch = $cardBodyRow->createColumn(['class' => 'col-md-4']);
        $columnShow = $cardBodyRow->createColumn(['class' => 'col-md-8']);

        // Search
        $search = new Ui\Components\Form\Elements\Input([
            'name'        => 'query',
            'data-role'   => 'table-crud-search',
            'placeholder' => language()->getLine('TABLE_LABEL_SEARCH'),
            'value'       => input()->get('query'),
        ]);

        $columnSearch->childNodes->push($search);

        // Show
        $inputGroup = new Ui\Components\Form\Input\Group();

        $addOn = new Ui\Components\Form\Input\Group\AddOn();
        $addOn->textContent->push(language()->getLine('TABLE_LABEL_SHOW'));

        $inputGroup->childNodes->push($addOn);

        $options = [];
        foreach (range((int)$this->config[ 'entries' ][ 'minimum' ], (int)$this->config[ 'entries' ][ 'maximum' ],
            (int)$this->config[ 'entries' ][ 'step' ]) as $entries) {
            $options[ $entries ] = $entries;
        }

        $columnsDropdown = new Ui\Components\Dropdown(language()->getLine('TABLE_LABEL_COLUMNS'));
        $columnsDropdown->attributes->addAttribute('data-role', 'table-crud-columns');

        foreach ($this->columns as $key => $column) {

            if ($column[ 'hiding' ] === false) {
                continue;
            }

            $label = new Ui\Components\Form\Elements\Label();
            $label->attributes->addAttributeClass('form-check-label');
            $label->textContent->push(language()->getLine($column[ 'label' ]));

            $checkbox = new Ui\Components\Form\Elements\Input([
                'type'        => 'checkbox',
                'class'       => 'form-check-input',
                'data-toggle' => 'table-crud-columns',
                'data-column' => $key,
            ]);

            $checkbox->attributes->removeAttributeClass('form-control');

            if ($column[ 'show' ] === true) {
                $checkbox->attributes->addAttribute('checked', 'checked');
            }

            $label->childNodes->push($checkbox);

            $columnsDropdown->menu->createItem($label);
        }

        $inputGroup->childNodes->push($columnsDropdown);

        $select = new Ui\Components\Form\Elements\Select();
        $select->attributes->addAttribute('name', 'entries');
        $select->createOptions($options, $showEntries);
        $inputGroup->childNodes->push($select);

        $addOn = new Ui\Components\Form\Input\Group\AddOn();
        $addOn->textContent->push(language()->getLine('TABLE_LABEL_PAGE', [
            $startNumber,
            $totalEntries,
            $totalRows,
            $currentPage,
            $totalPages,
        ]));

        $inputGroup->childNodes->push($addOn);

        $input = new Ui\Components\Form\Elements\Input([
            'name'        => 'page',
            'placeholder' => language()->getLine('TABLE_LABEL_GOTO'),
        ]);

        $inputGroup->childNodes->push($input);

        $pagination = new Ui\Components\Pagination($totalRows, $showEntries);
        $inputGroup->childNodes->push($pagination);

        $columnShow->childNodes->push($inputGroup);

        $card->textContent->push(parent::render());

        return $card->render();
    }

    protected function renderBodyColumn($td, array $column, Result\Row $row)
    {
        switch ($column[ 'format' ]) {
            default:
                if (is_callable($column[ 'format' ])) {
                    if (is_array($column[ 'field' ])) {
                        foreach ($column[ 'field' ] as $field) {
                            $args[ $field ] = $row->offsetGet($field);
                        }
                        $textContent = call_user_func_array($column[ 'format' ], $args);
                    } elseif ($row->offsetExists($column[ 'field' ])) {
                        $textContent = call_user_func_array($column[ 'format' ],
                            [$row->offsetGet($column[ 'field' ])]);
                    }

                    $td->textContent->push($textContent);

                } elseif (strpos($column[ 'format' ], '{{') !== false) {
                    $textContent = str_replace(['{{', '}}'], '', $column[ 'format' ]);
                    if (is_array($column[ 'field' ])) {
                        foreach ($column[ 'field' ] as $field) {
                            if ($row->offsetExists($field)) {
                                $textContent = str_replace('$' . $field, $row->offsetGet($field), $textContent);
                            } else {
                                $textContent = str_replace('$' . $field, '', $textContent);
                            }
                        }
                    } elseif ($row->offsetExists($column[ 'field' ])) {
                        $textContent = str_replace('$' . $column[ 'field' ], $row->offsetGet($column[ 'field' ]),
                            $textContent);
                    }

                    $td->textContent->push($textContent);
                }
                break;

            case 'checkbox':
                $checkbox = new Ui\Components\Form\Elements\Checkbox([
                    'data-role' => 'table-crud-checkbox',
                ]);
                $td->childNodes->push($checkbox);
                break;

            case 'number':
            case 'txt':
            case 'text':
            case 'price':
            case 'number':
                if ($row->offsetExists($column[ 'field' ])) {
                    $td->textContent->push($row->offsetGet($column[ 'field' ]));
                }
                break;
            case 'date':
                if ($row->offsetExists($column[ 'field' ])) {
                    print_out('date');
                }
                break;

            case 'status':
                if ($row->offsetExists($column[ 'field' ])) {
                    $options = [
                        'PUBLISH'   => 'success',
                        'UNPUBLISH' => 'warning',
                        'DRAFT'     => 'default',
                        'ARCHIVE'   => 'info',
                        'DELETE'    => 'danger',
                    ];

                    $badge = new Ui\Components\Badge(
                        language()->getLine('TABLE_OPTION_' . $row->offsetGet($column[ 'field' ])),
                        $options[ $row->offsetGet($column[ 'field' ]) ]
                    );

                    $td->childNodes->push($badge);
                }

                break;

            case 'actions':

                $buttons = new Ui\Components\Buttons\Group();

                foreach ($this->actions as $key => $action) {

                    if ($action[ 'show' ] === false) {
                        continue;
                    }

                    if ($action[ 'label' ] === true) {
                        $button = $buttons->createButton(language()->getLine('TABLE_BUTTON_' . strtoupper($key)));
                        if ( ! empty($action[ 'icon' ])) {
                            $button->setIcon($action[ 'icon' ]);
                        }
                    } else {
                        $button = $buttons->createButton(new Ui\Contents\Icon($action[ 'icon' ]));
                        $button->attributes->addAttribute('data-toggle', 'tooltip');
                        $button->attributes->addAttribute('title',
                            language()->getLine('TABLE_BUTTON_' . strtoupper($key)));
                    }

                    if (isset($action[ 'attr' ])) {
                        foreach ($action[ 'attr' ] as $name => $value) {
                            $button->attributes->addAttribute($name, $value);
                        }
                    }

                    $button->setContextualClass($action[ 'contextual' ]);
                    $button->smallSize();
                    $button->attributes->addAttribute('data-action', $key);
                }

                $td->childNodes->push($buttons);

                break;
        }
    }
}