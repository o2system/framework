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
 * Class MorphByManyThrough
 *
 * @package O2System\Framework\Models\Sql\Relations
 */
class MorphByManyThrough extends Sql\Relations\Abstracts\AbstractRelation
{
    /**
     * MorphByManyThrough::getResult
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function getResult()
    {
        $this->map->intermediaryModel->qb
            ->join($this->map->associateModel->table,
                $this->map->intermediaryModel->table . '.ownership_id=' . $this->map->associateModel->table . '.' . $this->map->associateModel->primaryKey .
                ' AND ' .
                $this->map->intermediaryModel->table . '.ownership_model="' . str_replace('\\', '\\\\', get_class($this->map->associateModel)) . '"'
            ,'INNER');

        $this->map->intermediaryModel->qb
            ->join($this->map->objectModel->table,
                $this->map->intermediaryModel->table . '.' . $this->map->morphKey . '_id=' . $this->map->objectModel->table . '.' . $this->map->objectModel->primaryKey .
                ' AND ' .
                $this->map->intermediaryModel->table . '.' . $this->map->morphKey . '_model="' . str_replace('\\', '\\\\', get_class($this->map->objectModel)) . '"'
                ,'INNER');

        $this->map->intermediaryModel->qb->select($this->map->associateModel->table . '.*');

        return $this->map->intermediaryModel->findWhere([
            $this->map->intermediaryModel->table . '.' . $this->map->morphKey . '_id' => $this->map->objectModel->row->offsetGet($this->map->objectPrimaryKey)
        ]);
    }
}
