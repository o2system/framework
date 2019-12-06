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
 * Class HasOne
 *
 * @package O2System\Framework\Models\Sql\Relations
 */
class HasOne extends Sql\Relations\Abstracts\AbstractRelation
{
    /**
     * HasOne::getResult
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function getResult()
    {
        if ($this->map->objectModel->row instanceof Sql\DataObjects\Result\Row) {
            $criteria = $this->map->objectModel->row->offsetGet($this->map->objectPrimaryKey);
            $field = $this->map->associateTable . '.' . $this->map->associateForeignKey;

            $this->map->associateModel->result = null;
            $this->map->associateModel->row = null;
            
            if ($result = $this->map->associateModel->find($criteria, $field, 1)) {
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
