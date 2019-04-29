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
        if ($this->map->currentModel->row instanceof Sql\DataObjects\Result\Row) {
            $criteria = $this->map->currentModel->row->offsetGet($this->map->currentPrimaryKey);
            $field = $this->map->referenceTable . '.' . $this->map->referenceForeignKey;

            if ($result = $this->map->referenceModel->find($criteria, $field, 1)) {
                return $result;
            }
        }

        return false;
    }
}