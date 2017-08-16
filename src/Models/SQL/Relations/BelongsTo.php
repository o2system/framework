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

use O2System\Framework\Abstracts\AbstractModel;
use O2System\Framework\Models\Abstracts\AbstractRelations;
use O2System\Framework\Models\Datastructures\Row;

/**
 * Class BelongsTo
 *
 * @package O2System\Framework\Models\SQL\Relations
 */
class BelongsTo extends AbstractRelations
{
    /**
     * Get Result
     *
     * @return bool|Row
     */
    public function getResult()
    {
        if ( $this->mapper->referenceModel instanceof AbstractModel ) {
            if ( $this->mapper->relationModel->row instanceof Row ) {
                if ( is_array( $this->mapper->relationForeignKey ) ) {
                    $criteria = $this->mapper->relationModel->row->{$this->mapper->relationModel->primaryKey};

                    $conditions = [];

                    foreach ( $this->mapper->relationForeignKey as $foreignKey => $foreignKeyCriteria ) {
                        if ( isset( $this->mapper->relationModel->row->{$foreignKey} ) ) {
                            $conditions[ $foreignKey ] = str_replace(
                                '{criteria}',
                                $this->mapper->relationModel->row->{$foreignKey},
                                $foreignKeyCriteria
                            );
                        } else {
                            $conditions[ $foreignKey ] = str_replace( '{criteria}', $criteria, $foreignKeyCriteria );
                        }
                    }

                    $result = $this->mapper->relationModel->findWhere( $conditions );
                } else {
                    $criteria = $this->mapper->relationModel->row->{$this->mapper->relationForeignKey};

                    $result = $this->mapper->referenceModel->find( $criteria, $this->mapper->referencePrimaryKey );
                }
            }

            if ( is_array( $result ) ) {
                return reset( $result );
            }
        } elseif ( isset( $this->mapper->referenceTable ) ) {
            if ( $this->mapper->relationModel->row instanceof Row ) {
                if ( is_array( $this->mapper->relationForeignKey ) ) {
                    $criteria = $this->mapper->relationModel->row->{$this->mapper->relationModel->primaryKey};

                    foreach ( $this->mapper->relationForeignKey as $foreignKey => $foreignKeyCriteria ) {
                        if ( isset( $this->mapper->relationModel->row->{$foreignKey} ) ) {
                            $conditions[ $foreignKey ] = str_replace(
                                '{criteria}',
                                $this->mapper->relationModel->row->{$foreignKey},
                                $foreignKeyCriteria
                            );
                        } else {
                            $conditions[ $foreignKey ] = str_replace( '{criteria}', $criteria, $foreignKeyCriteria );
                        }
                    }

                    $result = $this->mapper->referenceModel->db->getWhere(
                        $this->mapper->relationTable,
                        $conditions,
                        1
                    );
                } else {
                    $criteria = $this->mapper->relationModel->row->{$this->mapper->relationForeignKey};

                    $result = $this->mapper->relationModel->db->getWhere(
                        $this->mapper->referenceTable,
                        [
                            $this->mapper->referencePrimaryKey => $criteria,
                        ],
                        1
                    );
                }

                if ( $result->count() > 0 ) {
                    return $result->first();
                }
            }
        }

        return false;
    }
}