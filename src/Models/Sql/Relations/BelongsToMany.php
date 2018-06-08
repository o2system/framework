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

namespace O2System\Framework\Models\Sql\Relations;

// ------------------------------------------------------------------------

use O2System\Database\DataObjects\Result;
use O2System\Framework\Models\Sql;

/**
 * Class BelongsToMany
 *
 * @package O2System\Framework\Models\Sql\Relations
 */
class BelongsToMany extends Sql\Relations\Abstracts\AbstractRelation
{
    /**
     * Get Result
     *
     * @return Sql\DataObjects\Result|bool
     */
    public function getResult()
    {
        if ($this->map->relationModel->row instanceof Sql\DataObjects\Result\Row) {
            $result = $this->map->relationModel->qb
                ->from($this->map->referenceTable)
                ->join($this->map->pivotTable, implode(' = ', [
                    $this->map->pivotReferenceKey,
                    $this->map->referencePrimaryKey,
                ]))
                ->getWhere([$this->map->pivotRelationKey => $this->map->relationModel->row->offsetGet($this->map->relationPrimaryKey)]);

            if ($result instanceof Result) {
                if ($result->count() > 0) {
                    return (new Sql\DataObjects\Result($result, $this->map->relationModel))
                        ->setInfo($result->getInfo());
                }
            }
        }

        return false;
    }
}