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
 * Class BelongsToMany
 *
 * @package O2System\Framework\Models\Sql\Relations
 */
class BelongsToMany extends Sql\Relations\Abstracts\AbstractRelation
{
    /**
     * BelongsToMany::getResult
     * 
     * @return array|bool|Result
     */
    public function getResult()
    {
        if ($this->map->objectModel->row instanceof Sql\DataObjects\Result\Row) {
            $criteria = $this->map->objectModel->row->offsetGet($this->map->objectForeignKey);
            $condition = [
                $this->map->associateTable . '.' . $this->map->objectForeignKey => $criteria,
            ];

            $this->map->associateModel->result = null;
            $this->map->associateModel->row = null;

            if ($result = $this->map->associateModel->findWhere([$condition])) {
                return $result;
            }
        }

        return new Result([]);
    }
}