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
use O2System\Spl\Iterators\ArrayIterator;

/**
 * Class Result
 *
 * @package O2System\Database\DataStructures
 */
class Result extends ArrayIterator
{
    /**
     * Result::$info
     *
     * @var Info
     */
    public $info;

    // ------------------------------------------------------------------------

    /**
     * Result::__construct
     *
     * @param \O2System\Database\DataObjects\Result $result
     * @param \O2System\Framework\Models\Sql\Model  $model
     */
    public function __construct(\O2System\Database\DataObjects\Result $result, Model &$model)
    {
        if ($result->count() > 0) {
            $ormResult = new \SplFixedArray($result->count());

            foreach ($result as $key => $row) {
                $ormResult[ $key ] = new Result\Row($row, $model);
            }

            parent::__construct($ormResult->toArray());

            unset($ormResult);
        }
    }

    // ------------------------------------------------------------------------
    
    /**
     * Result::setInfo
     *
     * @param \O2System\Database\DataObjects\Result\Info $info
     *
     * @return static
     */
    public function setInfo(Info $info)
    {
        $this->info = $info;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Result::getInfo
     * 
     * @return \O2System\Database\DataObjects\Result\Info
     */
    public function getInfo()
    {
        return $this->info;
    }

    // ------------------------------------------------------------------------

    /**
     * Result::pagination
     * 
     * @return \O2System\Framework\Libraries\Ui\Components\Pagination
     */
    public function pagination()
    {
        $rows = $this->info->getTotal()->rows;
        $rows = empty($rows) ? 0 : $rows;

        $limit = input()->get('limit');
        $limit = empty($limit) ? $this->info->limit : $limit;

        return new Pagination($rows, $limit);
    }
}