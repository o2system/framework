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
 * Class MorphToMany
 *
 * @package O2System\Framework\Models\Sql\Relations
 */
class MorphToMany extends Sql\Relations\Abstracts\AbstractRelation
{
    /**
     * MorphToMany::getResult
     * 
     * @return array|bool|Result
     */
    public function getResult()
    {
        $morphKey = singular($this->map->morphKey);
        $this->map->associateModel->qb
            ->select($this->map->associateTable . '.*')
            ->from($this->map->associateTable)
            ->where([
                $this->map->associateTable . '.' . $morphKey . '_id' => $this->map->objectModel->row->offsetGet($this->map->objectPrimaryKey),
                $this->map->associateTable . '.' . $morphKey . '_model' => get_class($this->map->objectModel)
            ]);

        if ($result = $this->map->associateModel->qb->get()) {
            $this->map->associateModel->result = new Sql\DataObjects\Result($result, $this->map->associateModel);
        }

        return $this->map->associateModel->result;
    }
}