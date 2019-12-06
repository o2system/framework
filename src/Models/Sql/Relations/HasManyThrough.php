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

namespace O2System\Framework\Models\Sql\Relations;

// ------------------------------------------------------------------------

use O2System\Database\DataObjects\Result;
use O2System\Framework\Models\Sql;

/**
 * Class HasManyThrough
 *
 * @package O2System\Framework\Models\Sql\Relations
 */
class HasManyThrough extends Sql\Relations\Abstracts\AbstractRelation
{
    /**
     * HasManyThrough::getResult
     * 
     * @return array|bool|Result
     */
    public function getResult()
    {
        if ($this->map->objectModel->row instanceof Sql\DataObjects\Result\Row) {
            $criteria = $this->map->objectModel->row->offsetGet($this->map->objectPrimaryKey);
            $conditions = [
                $this->map->intermediaryTable . '.' . $this->map->intermediaryForeignKey => $criteria,
            ];

            $this->map->intermediaryModel->qb
                ->select([
                    $this->map->associateTable . '.*',
                ])
                ->from($this->map->intermediaryTable)
                ->join($this->map->associateTable, implode(' = ', [
                    $this->map->associateTable . '.' . $this->map->associatePrimaryKey,
                    $this->map->intermediaryTable . '.' . $this->map->intermediaryAssociateForeignKey,
                ]));

            $this->map->intermediaryModel->result = null;
            $this->map->intermediaryModel->row = null;

            if ($result = $this->map->intermediaryModel->findWhere($conditions)) {
                if($result->count()) {
                    $ids = [];
                    foreach($result as $row) {
                        $ids[$row->id] = $row->id;
                    }

                    if($result = $this->map->associateModel->findIn($ids)) {
                        return $result;
                    }
                }
            }
        }

        return new Result([]);
    }
}
