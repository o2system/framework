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

use O2System\Framework\Models\Interfaces\Relations;
use O2System\Framework\Models\Interfaces\Table;

/**
 * Class With
 *
 * @package O2System\Framework\Models\SQL\Relations
 */
class With
{
    protected $_relationships = [];

    /**
     * Set Relations
     *
     * @access  public
     *
     * @param   array $references list of references
     */
    public function setRelationships( array $relationsips )
    {
        foreach ( $relationsips as $relationship ) {
            $this->_setRelationship( $relationship );
        }
    }

    // ------------------------------------------------------------------------

    protected function _setRelationship( $relation )
    {
        // Try to load reference model
        $relation_model = $this->_loadRelationModel( $relation );

        $relationship = new \stdClass();

        if ( $relation_model instanceof Model ) {
            $relationship->model = $relation_model;
            $relationship->table = $relation_model->table;
        } else {
            if ( strpos( $relation, '.' ) !== false ) {
                $x_reference = explode( '.', $relation );

                $relationship->table = $x_reference[ 0 ];
                $relationship->field = $x_reference[ 1 ];
            } else {
                $relationship->table = $relation;
            }
        }

        if ( ! isset( $relationship->field ) ) {
            $reference_table = str_replace( Table::$prefixes, '', $this->referenceTable );
            $reference_field = singular( $reference_table );

            $relation_fields = [
                'id_' . $reference_field,
                $reference_field . '_id',
            ];

            foreach ( $relation_fields as $relation_field ) {
                if ( isset( $this->relationModel ) ) {
                    $relationship->fields = $this->relationModel->fields;
                } else {
                    $relationship->fields = $this->referenceModel->db->listFields( $relationship->table );
                }

                if ( in_array( $relation_field, $relationship->fields ) ) {
                    $relationship->field = $relation_field;
                }
            }
        }

        $prefixes = Table::$prefixes;
        array_unshift( $prefixes, $this->referenceTable );
        $relationship->index = trim( str_replace( $prefixes, '', $relationship->table ), '_' );

        $this->_relationships[ $relationship->index ] = $relationship;
    }


    /**
     * Result
     *
     * Only for implements Relation::result() method
     *
     * @return NULL
     */
    public function result()
    {
        if ( ! empty( $this->_relationships ) ) {
            $selects[] = $this->referenceTable . '.*';

            foreach ( $this->_relationships as $relationship ) {
                $this->referenceModel->db->join(
                    $relationship->table,
                    $relationship->table . '.' . $relationship->field . ' = ' . $this->referenceTable . '.' . $this->referenceField
                );

                foreach ( $relationship->fields as $field ) {
                    $selects[] = $relationship->table . '.' . $field . ' AS ' . $relationship->index . '_' . $field;
                }
            }

            $this->referenceModel->db->select( implode( ', ', $selects ) );

            return $this->_relationships;
        }

        return [];
    }
}