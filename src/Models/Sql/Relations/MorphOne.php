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
 * Class MorphOne
 *
 * @package O2System\Framework\Models\Sql\Relations
 */
class MorphOne extends Sql\Relations\Abstracts\AbstractRelation
{
    /**
     * MorphOne::getResult
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
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

        return $this->map->associateModel->findWhere($conditions, 1);
    }
}
