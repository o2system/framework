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

namespace O2System\Framework\Models\Sql\DataObjects;

// ------------------------------------------------------------------------

use O2System\Database\DataObjects\Result\Info;
use O2System\Framework\Libraries\Ui\Components\Pagination;
use O2System\Framework\Models\Sql\Model;
use O2System\Spl\Info\SplClassInfo;

/**
 * Class Result
 *
 * @package O2System\Database\DataStructures
 */
class Result extends \O2System\Database\DataObjects\Result
{
    /**
     * Result::$model
     *
     * @var Model
     */
    protected $model;

    /**
     * Result::$info
     *
     * @var Info
     */
    protected $info;

    // ------------------------------------------------------------------------

    /**
     * Result::__construct
     *
     * @param \O2System\Database\DataObjects\Result $result
     * @param \O2System\Framework\Models\Sql\Model  $model
     */
    public function __construct(\O2System\Database\DataObjects\Result $result, Model &$model)
    {
        $this->model = new SplClassInfo($model);

        if ( ! models()->has($this->model->getClass())) {
            models()->add($model, $this->model->getClass());
        }

        $this->info = $result->getInfo();
        parent::__construct($result->toArray());
    }

    // ------------------------------------------------------------------------

    /**
     * Result::offsetSet
     *
     * @param mixed $offset
     * @param mixed $row
     */
    public function offsetSet($offset, $row)
    {
        $model = models($this->model->getClass());

        if (method_exists($model, 'rebuildRow')) {
            $row = $model->rebuildRow($row);
        }

        $hideColumns = [];

        // Visible Columns
        if (count($model->visibleColumns)) {
            $hideColumns = array_diff($row->getColumns(), $model->visibleColumns);
        }

        // Hide Columns
        if (count($model->hideColumns)) {
            $hideColumns = array_merge($model->hideColumns);
        }

        // Unset Columns
        foreach ($hideColumns as $column) {
            $row->offsetUnset($column);
        }

        parent::offsetSet($offset, new Result\Row($row, $model));
    }

    // ------------------------------------------------------------------------

    /**
     * Result::pagination
     *
     * @return \O2System\Framework\Libraries\Ui\Components\Pagination
     */
    public function pagination()
    {
        $rows = $this->info->num_rows;
        $rows = empty($rows) ? 0 : $rows;

        $limit = input()->get('limit');
        $limit = empty($limit) ? $this->info->limit : $limit;

        return new Pagination($rows, $limit);
    }
}