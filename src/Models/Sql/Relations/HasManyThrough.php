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
 * Class HasManyThrough
 *
 * @package O2System\Framework\Models\Sql\Relations
 */
class HasManyThrough extends Sql\Relations\Abstracts\AbstractRelation
{
    /**
     * Get Result
     *
     * @return Sql\DataObjects\Result|bool
     */
    public function getResult()
    {
        // print_out($this->map);
        if ($this->map->pivotModel->row instanceof Sql\DataObjects\Result\Row) {
            $result = $this->map->pivotModel->qb
                ->from($this->map->relationTable)
                ->join($this->map->referenceTable, implode(' = ', [
                    $this->map->referencePrimaryKey,
                    $this->map->relationForeignKey,
                ]))
                ->getWhere([$this->map->referencePrimaryKey => $this->map->pivotModel->row->offsetGet($this->map->pivotForeignKey)]);

            if ($result instanceof Result) {
                if ($result->count() > 0) {
                    if ($this->map->relationModel instanceof Sql\Model) {
                        return (new Sql\DataObjects\Result($result, $this->map->relationModel))
                            ->setInfo($result->getInfo());
                    }

                    $pivotModel = new class extends Sql\Model {};
                    $pivotModel->table = $this->map->pivotTable;

                    return (new Sql\DataObjects\Result($result, $pivotModel))->setInfo($result->getInfo());
                }
            }
        }

        return false;
    }
}