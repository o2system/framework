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
 * Class MorphOneThrough
 *
 * @package O2System\Framework\Models\Sql\Relations
 */
class MorphOneThrough extends Sql\Relations\Abstracts\AbstractRelation
{
    /**
     * MorphOneThrough::getResult
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function getResult()
    {
        if(empty($this->map->objectModel->primaryKeys)) {
            $ownershipIdField = $this->map->objectModel->table . '.' . $this->map->objectModel->primaryKey;
            $conditions = [
                $this->map->intermediaryModel->table . '.ownership_id' => $this->map->objectModel->row->offsetGet($this->map->objectPrimaryKey)
            ];
        } else {
            $ownershipId = [];
            $ownershipIdField = 'CONCAT(';
            $i = 0;
            foreach($this->map->objectModel->primaryKeys as $primaryKey) {
                $ownershipIdField.= $this->map->objectModel->table . '.' . $primaryKey;

                if($i == 0 and $i != count($this->map->objectModel->primaryKeys)) {
                    $ownershipIdField.= ', "-", ';
                }

                $ownershipId[] = $this->map->objectModel->row->offsetGet($primaryKey);

                $i++;
            }
            $ownershipIdField.= ')';

            $conditions = [
                $this->map->intermediaryModel->table . '.ownership_id' => implode('-', $ownershipId)
            ];
        }

        $this->map->associateModel->qb
            ->select($this->map->associateModel->table . '.*')
            ->from($this->map->associateModel->table)
            ->whereIn('id', $this->map->associateModel->qb->subQuery()
                ->select($this->map->intermediaryModel->table . '.' . $this->map->morphKey . '_id')
                ->from($this->map->intermediaryModel->table)
                ->join($this->map->objectModel->table,
                    $this->map->intermediaryModel->table . '.ownership_id=' . $ownershipIdField .
                    ' AND ' .
                    $this->map->intermediaryModel->table . '.ownership_model="' . str_replace('\\', '\\\\', get_class($this->map->objectModel)) . '"'
                    ,'INNER')
                ->join($this->map->associateModel->table,
                    $this->map->intermediaryModel->table . '.' . $this->map->morphKey . '_id=' . $this->map->associateModel->table . '.' . $this->map->associateModel->primaryKey .
                    ' AND ' .
                    $this->map->intermediaryModel->table . '.' . $this->map->morphKey . '_model="' . str_replace('\\', '\\\\', get_class($this->map->associateModel)) . '"'
                    ,'INNER')
                ->where($conditions));

        if ($result = $this->map->associateModel->qb->limit(1)->get()) {
            $this->map->associateModel->result = new Sql\DataObjects\Result($result, $this->map->associateModel);

            if ($this->map->associateModel->result->count() == 1) {
                return $this->map->associateModel->result->first();
            }
        }

        return false;
    }
}
