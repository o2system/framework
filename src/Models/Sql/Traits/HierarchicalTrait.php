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

namespace O2System\Framework\Models\Sql\Traits;

// ------------------------------------------------------------------------

/**
 * Class HierarchicalTrait
 *
 * @package O2System\Framework\Models\Sql\Traits
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
     * @return int  Right column value
     */
    final public function afterProcessRebuild($idParent = 0, $left = 0, $depth = 0)
    {
        /* the right value of this node is the left value + 1 */
        $right = $left + 1;

        /* get all children of this node */
        $result = $this->qb
            ->table($this->table)
            ->select('id')
            ->where('id_parent', $idParent)
            ->orderBy('record_ordering')
            ->get();

        if ($result) {
            foreach ($result as $row) {
                /* does this page have children? */
                $right = $this->afterProcessRebuild($row->id, $right, $depth + 1);
            }
        }

        /* update this page with the (possibly) new left, right, and depth values */
        $this->qb
            ->table($this->table)
            ->where('id', $idParent)
            ->update([
                'record_left'  => $left,
                'record_right' => $right,
                'record_depth' => $depth - 1,
            ]);

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
    final public function getParents($id, &$parents = [])
    {
        $result = $this->qb
            ->table($this->table)
            ->whereIn('id', $this->qb
                ->subQuery()
                ->table($this->table)
                ->select('id_parent')
                ->where('id', $id)
            )
            ->get();

        if ($result) {
            if($result->count()) {
                $parents[] = $row = $result->first();

                if($this->hasParent($row->id_parent)) {
                    $this->getParents($row->id, $parents);
                }
            }
        }

        return array_reverse($parents);
    }
    // ------------------------------------------------------------------------

    final public function hasParent($idParent)
    {
        $result = $this->qb
            ->table($this->table)
            ->select('id')
            ->where('id', $idParent)
            ->get();

        if ($result) {
            return (bool)($result->count() == 0 ? false : true);
        }

        return false;
    }

    /**
     * Find Childs
     *
     * Retreive all childs
     *
     * @param numeric $idParent Parent ID
     *
     * @access public
     * @return array
     */
    public function getChilds($idParent)
    {
        $result = $this->qb
            ->table($this->table)
            ->where('id_parent', $idParent)
            ->get();

        $childs = [];

        if ($result) {
            foreach($result as $row) {
                $childs[] = $row;
                if( $this->hasChild( $row->id ) ) {
                    $row->childs = $this->getChilds($row->id);
                }
            }
        }

        return $childs;
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
    final public function hasChild($idParent)
    {
        $result = $this->qb
            ->table($this->table)
            ->select('id')
            ->where('id_parent', $idParent)
            ->get();

        if ($result) {
            return (bool)($result->count() == 0 ? false : true);
        }

        return false;
    }
    // ------------------------------------------------------------------------

    /**
     * Count Childs
     *
     * Num childs of a record
     *
     * @param numeric $idParent Record Parent ID
     *
     * @access public
     * @return bool
     */
    final public function getNumChilds($idParent)
    {
        $result = $this->qb
            ->table($this->table)
            ->select('id')
            ->where('id_parent', $idParent)
            ->get();

        if ($result) {
            return $result->count();
        }

        return 0;
    }
}