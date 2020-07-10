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
 * Class MorphByOne
 *
 * @package O2System\Framework\Models\Sql\Relations
 */
class MorphByOne extends Sql\Relations\Abstracts\AbstractRelation
{
    /**
     * MorphByMany::getResult
     * 
     * @return array|bool|Result
     */
    public function getResult()
    {
        $morphKey = plural($this->map->morphKey);
        $this->map->setIntermediary($intermediaryModel = get_class($this->map->objectModel) . '\\' . studlycase($morphKey));
        
        if( ! $this->map->intermediaryModel instanceof $intermediaryModel) {
            $this->map->setIntermediary($this->map->objectTable . '_' . underscore($morphKey));
        }

        $morphKey = singular($this->map->morphKey);
        $this->map->associateModel->qb->whereIn(
            $this->map->associateTable . '.' . $this->map->associatePrimaryKey,
            $this->map->associateModel->qb->subQuery()
                ->from($this->map->intermediaryTable)
                ->select($this->map->intermediaryTable . '.' . $morphKey . '_id')
                ->where([
                    $this->map->intermediaryTable . '.' . $this->map->intermediaryForeignKey => $this->map->objectModel->row->offsetGet($this->map->objectPrimaryKey),
                    $morphKey . '_model' => get_class($this->map->associateModel)
                ])
        );

        if ($result = $this->map->associateModel->qb->limit(1)->get()) {
            $this->map->associateModel->result = new Sql\DataObjects\Result($result, $this->map->associateModel);

            if ($this->map->associateModel->result->count() == 1) {
                return $this->map->associateModel->result->first();
            }
        }

        return false;
    }
}