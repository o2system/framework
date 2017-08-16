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
 * Class HasMany
 *
 * @package O2System\Framework\Models\SQL\Relations
 */
class HasMany extends AbstractRelations
{
    /**
     * @return array
     */
    public function getResult()
    {
        if ( $this->mapper->relationModel instanceof AbstractModel ) {
            if ( $this->mapper->referenceModel->row instanceof Row ) {
                $criteria = $this->mapper->referenceModel->row->{$this->mapper->referencePrimaryKey};

                if ( is_array( $this->mapper->relationForeignKey ) ) {
                    $conditions = [];

                    foreach ( $this->mapper->relationForeignKey as $foreignKey => $foreignKeyCriteria ) {
                        $conditions[ $foreignKey ] = str_replace( '{criteria}', $criteria, $foreignKeyCriteria );
                    }

                    return $this->mapper->relationModel->findWhere( $conditions );
                } else {
                    return $this->mapper->relationModel->find( $criteria, $this->mapper->relationForeignKey );
                }
            }
        } elseif ( isset( $this->mapper->relationTable ) ) {
            if ( $this->mapper->referenceModel->row instanceof Row ) {
                $criteria = $this->mapper->referenceModel->row->{$this->mapper->referencePrimaryKey};

                if ( is_array( $this->mapper->relationForeignKey ) ) {
                    foreach ( $this->mapper->relationForeignKey as $foreignKey => $foreignKeyCriteria ) {
                        $conditions[ $foreignKey ] = str_replace( '{criteria}', $criteria, $foreignKeyCriteria );
                    }

                    $result = $this->mapper->referenceModel->db->getWhere(
                        $this->mapper->relationTable,
                        $conditions,
                        1
                    );
                } else {
                    $result = $this->mapper->referenceModel->db->getWhere(
                        $this->mapper->relationTable,
                        [
                            $this->mapper->relationForeignKey => $criteria,
                        ],
                        1
                    );
                }

                return $result;
            }
        }

        return false;
    }
}