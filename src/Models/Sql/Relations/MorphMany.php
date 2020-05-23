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
 * Class MorphMany
 *
 * @package O2System\Framework\Models\Sql\Relations
 */
class MorphMany extends Sql\Relations\Abstracts\AbstractRelation
{
    /**
     * MorphMany::getResult
     * 
     * @return array|bool|Result
     */
    public function getResult()
    {
        $morphKey = singular($this->map->morphKey);

        if(empty($this->map->objectPrimaryKeys)) {
            $conditions[ $this->map->associateTable . '.' . $morphKey . '_id' ] = $this->map->objectModel->row->offsetGet($this->map->objectPrimaryKey);
        } else {
            foreach($this->map->objectPrimaryKeys as $objectPrimaryKey) {
                $conditions[ $this->map->associateTable . '.' . $morphKey . '_id' ][] = $this->map->objectModel->row->offsetGet($objectPrimaryKey);
            }

            $conditions[ $this->map->associateTable . '.' . $morphKey . '_id' ] = implode('-', $conditions[ $this->map->associateTable . '.' . $morphKey . '_id' ]);
        }

        $conditions[ $this->map->associateTable . '.' . $morphKey . '_model' ] = get_class($this->map->objectModel);

        if ($result = $this->map->associateModel->findWhere($conditions)) {
            return $result;
        }

        return new Result([]);
    }
}