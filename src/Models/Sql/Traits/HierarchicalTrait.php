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
    final public function rebuild($idParent = 0, $left = 1, $depth = 0)
    {
        ini_set('xdebug.max_nesting_level', 10000);
        ini_set('memory_limit', '-1');

        $result = $this->qb
            ->table($this->table)
            ->select('id')
            ->where('id_parent',$idParent)
            ->orderBy('id')
            ->get();

        $right = $left + 1;

        if($result) {
            $i = 0;
            foreach($result as $row) {
                if($i == 0) {
                    $this->qb
                        ->table($this->table)
                        ->where('id', $row->id)
                        ->update($update = [
                            'record_left'  => $left,
                            'record_right' => $right,
                            'record_depth' => $depth
                        ]);
                } else {
                    $this->qb
                        ->table($this->table)
                        ->where('id', $row->id)
                        ->update($update = [
                            'record_left'  => $left = $right+1,
                            'record_right' => $right = $left+1,
                            'record_depth' => $depth
                        ]);
                }

                $update['id'] = $row->id;

                if($this->hasChild($row->id)) {
                    $right = $this->rebuild($row->id, $right, $depth + 1);
                    $this->qb
                        ->table($this->table)
                        ->where('id', $row->id)
                        ->update($update = [
                            'record_right' => $right
                        ]);
                    $update['id'] = $row->id;
                }

                $i++;
            }
        }

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