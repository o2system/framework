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
        $morphKey = plural($this->map->morphKey);
        $this->map->setIntermediary($intermediaryModel = get_class($this->map->associateModel) . '\\' . studlycase($morphKey));

        if( ! $this->map->intermediaryModel instanceof $intermediaryModel) {
            $this->map->setIntermediary($this->map->associateTable . '_' . underscore($morphKey));
        }

        $morphKey = singular($this->map->morphKey);
        $this->map->associateModel->qb->whereIn(
            $this->map->associateTable . '.' . $this->map->associatePrimaryKey,
            $this->map->associateModel->qb->subQuery()
                ->from($this->map->intermediaryTable)
                ->select($this->map->intermediaryTable . '.' . $this->map->intermediaryAssociateForeignKey)
                ->where([
                    $this->map->intermediaryTable . '.' . $morphKey . '_id' => $this->map->objectModel->row->offsetGet($this->map->objectPrimaryKey),
                    $this->map->intermediaryTable . '.' . $morphKey . '_model' => get_class($this->map->objectModel)
                ])
        );

        if ($result = $this->map->associateModel->all()) {
            if($result->count()) {
                return $result->first();
            }
        }

        return false;
    }
}
