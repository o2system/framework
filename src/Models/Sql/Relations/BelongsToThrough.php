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
 * Class BelongsToThrough
 *
 * @package O2System\Framework\Models\Sql\Relations
 */
class BelongsToThrough extends Sql\Relations\Abstracts\AbstractRelation
{
    /**
     * BelongsToThrough::getResult
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function getResult()
    {
        if ($this->map->objectModel->row instanceof Sql\DataObjects\Result\Row) {
            $criteria = $this->map->objectModel->row->offsetGet($this->map->objectPrimaryKey);
            $field = $this->map->objectTable . '.' . $this->map->objectPrimaryKey;

            $this->map->associateModel->qb
                ->select([
                    $this->map->associateTable . '.*',
                ])
                ->join($this->map->objectTable, implode(' = ', [
                    $this->map->objectTable . '.' . $this->map->objectPrimaryKey,
                    $this->map->intermediaryTable . '.' . $this->map->intermediaryPrimaryKey,
                ]))
                ->join($this->map->associateTable, implode(' = ', [
                    $this->map->associateTable . '.' . $this->map->associatePrimaryKey,
                    $this->map->intermediaryTable . '.' . $this->map->intermediaryAssociateForeignKey,
                ]));

            $this->map->intermediaryModel->result = null;
            $this->map->intermediaryModel->row = null;

            if ($result = $this->map->intermediaryModel->find($criteria, $field, 1)) {
                if($result instanceof Sql\DataObjects\Result\Row) {
                    return $result;
                } elseif($result instanceof Sql\DataObjects\Result) {
                    return $result->first();
                }
            }
        }

        return false;
    }
}
