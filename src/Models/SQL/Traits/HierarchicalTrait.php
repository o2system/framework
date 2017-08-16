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

namespace O2System\Framework\Models\SQL\Traits;

// ------------------------------------------------------------------------

/**
 * Class HierarchicalTrait
 *
 * @package O2System\Framework\Models\SQL\Traits
 */
trait HierarchicalTrait
{
    /**
     * Rebuild Tree
     *
     * Rebuild self hierarchical table
     *
     * @access public
     *
     * @param string $table Working database table
     *
     * @return numeric  rgt column value
     */
    final public function _afterProcessRebuild( $id_parent = 0, $left = 0, $depth = 0 )
    {
        $table = empty( $table ) ? $this->table : $table;

        /* the right value of this node is the left value + 1 */
        $right = $left + 1;

        /* get all children of this node */
        $this->db->select( 'id' )->where( 'id_parent', $id_parent )->orderBy( 'record_ordering' );
        $query = $this->db->get( $table );

        if ( $query->count() > 0 ) {
            foreach ( $query->result() as $row ) {
                /* does this page have children? */
                $right = $this->rebuildTree( $table, $row->id, $right, $depth + 1 );
            }
        }

        /* update this page with the (possibly) new left, right, and depth values */
        $data = [ 'record_left' => $left, 'record_right' => $right, 'record_depth' => $depth - 1 ];
        $this->db->update( $table, $data, [ 'id' => $id_parent ] );

        /* return the right value of this node + 1 */

        return $right + 1;
    }

    /**
     * Find Parents
     *
     * Retreive parents of a record
     *
     * @param numeric $id Record ID
     *
     * @access public
     * @return array
     */
    final public function getParents( $id = 0, &$parents = [] )
    {
        $result = $this->db->getWhere( $this->table, [ 'id' => $id ] );

        if ( $result->count() > 0 ) {
            $parents[] = $result->first();

            if ( (int)$result->first()->id_parent > 0 ) {
                $this->getRowParents( $result->first()->id_parent, $parents );
            }
        }

        return array_reverse( $parents );
    }
    // ------------------------------------------------------------------------

    /**
     * Find Childs
     *
     * Retreive all childs
     *
     * @param numeric $id_parent Parent ID
     *
     * @access public
     * @return array
     */
    public function getChilds( $id_parent = null )
    {
        if ( isset( $id_parent ) ) {
            $this->db->where( 'id_parent', $id_parent )->orWhere( 'id', $id_parent );
        }

        if ( $this->db->fieldExists( 'record_left', $this->table ) ) {
            $this->db->orderBy( 'record_left', 'ASC' );
        }

        if ( $this->db->fieldExists( 'record_ordering', $this->table ) ) {
            $this->db->orderBy( 'record_ordering', 'ASC' );
        }

        $query = $this->db->get( $this->table );

        if ( $query->count() > 0 ) {
            return $query->result();
        }

        return [];
    }
    // ------------------------------------------------------------------------

    /**
     * Has Childs
     *
     * Check if there is a child rows
     *
     * @param string $table Working database table
     *
     * @access public
     * @return bool
     */
    final public function hasChilds( $id_parent = 0 )
    {
        $query = $this->db->select( 'id' )->where( 'id_parent', $id_parent )->get( $this->table );

        if ( $query->count() > 0 ) {
            return true;
        }

        return false;
    }
    // ------------------------------------------------------------------------

    /**
     * Count Childs
     *
     * Num childs of a record
     *
     * @param numeric $id_parent Record Parent ID
     *
     * @access public
     * @return bool
     */
    final public function countChilds( $id_parent )
    {
        return $this->db->select( 'id' )->getWhere( $this->table, [ 'id_parent' => $id_parent ] )->count();
    }
    // ------------------------------------------------------------------------
}