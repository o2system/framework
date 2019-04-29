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
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function getResult()
    {
        if ($this->map->currentModel->row instanceof Sql\DataObjects\Result\Row) {
            $criteria = $this->map->currentModel->row->offsetGet($this->map->currentPrimaryKey);
            $condition = [
                $this->map->intermediaryTable . '.' . $this->map->intermediaryCurrentForeignKey => $criteria,
            ];

            $this->map->intermediaryModel->qb
                ->select([
                    $this->map->referenceTable . '.*',
                ])
                ->from($this->map->intermediaryTable)
                ->join($this->map->referenceTable, implode(' = ', [
                    $this->map->referenceTable . '.' . $this->map->referencePrimaryKey,
                    $this->map->intermediaryTable . '.' . $this->map->intermediaryReferenceForeignKey,
                ]));

            if ($result = $this->map->intermediaryModel->findWhere($condition)) {
                return $result;
            }
        }

        return false;
    }
}