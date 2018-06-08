<?php
/**
 * This file is part of the O2System PHP Framework package.
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
 * Class BelongsTo
 *
 * @package O2System\Framework\Models\Sql\Relations
 */
class BelongsTo extends Abstracts\AbstractRelation
{
    /**
     * Get Result
     *
     * @return \O2System\Framework\Models\Sql\DataObjects\Result\Row|bool
     */
    public function getResult()
    {
        if ($this->map->relationModel->row instanceof Sql\DataObjects\Result\Row) {

            $criteria = $this->map->relationModel->row->offsetGet($this->map->relationForeignKey);
            $conditions = [$this->map->referencePrimaryKey => $criteria];

            if ($this->map->referenceModel instanceof Sql\Model) {
                $result = $this->map->relationModel->qb
                    ->from($this->map->referenceModel->table)
                    ->getWhere($conditions, 1);

                if ($result instanceof Result) {
                    if ($result->count() > 0) {
                        $this->map->referenceModel->result = (new Sql\DataObjects\Result($result,
                            $this->map->referenceModel))->setInfo($result->getInfo());

                        return $this->map->referenceModel->row = $this->map->referenceModel->result->first();
                    }
                }
            } elseif ( ! empty($this->map->referenceTable)) {
                $result = $this->map->relationModel->qb
                    ->from($this->map->referenceTable)
                    ->getWhere($conditions, 1);

                if ($result instanceof Result) {
                    if ($result->count() > 0) {
                        $referenceModel = new class extends Sql\Model {};
                        $referenceModel->table = $this->map->referenceTable;

                        return (new Sql\DataObjects\Result($result, $referenceModel))
                            ->setInfo($result->getInfo())->first();
                    }
                }
            }
        }

        return false;
    }
}