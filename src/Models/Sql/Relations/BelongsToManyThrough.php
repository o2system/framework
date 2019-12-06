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
 * Class BelongsToManyThrough
 *
 * @package O2System\Framework\Models\Sql\Relations
 */
class BelongsToManyThrough extends Sql\Relations\Abstracts\AbstractRelation
{
    /**
     * BelongsToManyThrough::getResult
     * 
     * @return array|bool|Result
     */
    public function getResult()
    {
        if ($this->map->objectModel->row instanceof Sql\DataObjects\Result\Row) {
            $criteria = $this->map->objectModel->row->offsetGet($this->map->objectPrimaryKey);
            $conditions = [
                $this->map->objectTable . '.' . $this->map->objectPrimaryKey => $criteria,
            ];

            $this->map->associateModel->qb
                ->select([
                    $this->map->associateTable . '.*',
                ])
                ->join($this->map->objectTable, implode(' = ', [
                    $this->map->objectTable . '.' . $this->map->objectPrimaryKey,
                    $this->map->intermediaryTable . '.' . $this->map->intermediaryForeignKey,
                ]))
                ->join($this->map->associateTable, implode(' = ', [
                    $this->map->associateTable . '.' . $this->map->associatePrimaryKey,
                    $this->map->intermediaryTable . '.' . $this->map->intermediaryAssociateForeignKey,
                ]));

            $this->map->intermediaryModel->result = null;
            $this->map->intermediaryModel->row = null;

            if ($result = $this->map->intermediaryModel->findWhere($conditions)) {
                return $result;
            }
        }

        return new Result([]);
    }
}