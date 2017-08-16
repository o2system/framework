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

namespace O2System\Framework\Models\SQL\Relations;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Abstracts\AbstractModel;
use O2System\Framework\Models\Abstracts\AbstractRelations;
use O2System\Framework\Models\Datastructures\Row;

/**
 * Class HasOne
 *
 * @package O2System\Framework\Models\SQL\Relations
 */
class HasOne extends AbstractRelations
{
    /**
     * Get Result
     *
     * @return null
     */
    public function getResult()
    {
        if ( $this->mapper->relationModel instanceof AbstractModel ) {
            if ( $this->mapper->referenceModel->row instanceof Row ) {
                $criteria = $this->mapper->referenceModel->row->{$this->mapper->referencePrimaryKey};

                return $this->mapper->relationModel->find( $criteria, $this->mapper->relationForeignKey );
            }
        } elseif ( isset( $this->mapper->relationTable ) ) {
            if ( $this->mapper->referenceModel->row instanceof Row ) {
                $criteria = $this->mapper->referenceModel->row->{$this->mapper->referencePrimaryKey};

                $result = $this->mapper->referenceModel->db->getWhere(
                    $this->mapper->relationTable,
                    [
                        $this->mapper->relationForeignKey => $criteria,
                    ],
                    1
                );

                if ( $result->count() > 0 ) {
                    return $result->first();
                }
            }
        }

        return false;
    }
}