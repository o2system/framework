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
class BelongsToThrough extends Sql\Relations\Abstracts\AbstractRelation
{
    public function getResult()
    {
        if ($this->map->currentModel->row instanceof Sql\DataObjects\Result\Row) {
            $criteria = $this->map->currentModel->row->offsetGet($this->map->currentPrimaryKey);
            $field = $this->map->intermediaryTable . '.' . $this->map->intermediaryCurrentForeignKey;

            $this->map->referenceModel->qb
                ->select([
                    $this->map->referenceTable . '.*'
                ])
                ->join($this->map->intermediaryTable, implode(' = ', [
                    $this->map->intermediaryTable . '.' . $this->map->intermediaryReferenceForeignKey,
                    $this->map->referenceTable . '.' . $this->map->referencePrimaryKey
                ]));

            if ($result = $this->map->referenceModel->find($criteria, $field, 1)) {
                return $result;
            }
        }

        return false;
    }
}