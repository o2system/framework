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

        parent::__construct($result->toArray());

        $this->info = $result->getInfo();
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
        if($model = models($this->model->getClass())) {
            $row = new Result\Row($row, $model);
        }

        parent::offsetSet($offset, $row);
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