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
     * Parent Key Field
     *
     * @access  public
     * @type    string
     */
    public $parentKey = 'id_parent';

    /**
     * Hierarchical Enabled Flag
     *
     * @access  protected
     * @type    bool
     */
    protected $hierarchical = true;

    // ------------------------------------------------------------------------

    /**
     * HierarchicalTrait::rebuildTree
     * 
     * @param int $idParent
     * @param int $left
     * @param int $depth
     *
     * @return int
     */
    protected function rebuildTree($idParent = 0, $left = 1, $depth = 0)
    {
        ini_set('xdebug.max_nesting_level', 10000);
        ini_set('memory_limit', '-1');

        if ($result = $this->qb
            ->table($this->table)
            ->select('id')
            ->where('id_parent', $idParent)
            ->orderBy('id')
            ->get()) {

            $right = $left + 1;

            $i = 0;
            foreach ($result as $row) {
                if ($i == 0) {
                    $this->qb
                        ->from($this->table)
                        ->where('id', $row->id)
                        ->update($update = [
                            'record_left'  => $left,
                            'record_right' => $right,
                            'record_depth' => $depth,
                        ]);
                } else {
                    $this->qb
                        ->from($this->table)
                        ->where('id', $row->id)
                        ->update($update = [
                            'record_left'  => $left = $right + 1,
                            'record_right' => $right = $left + 1,
                            'record_depth' => $depth,
                        ]);
                }

                $update[ 'id' ] = $row->id;

                if ($this->hasChilds($row->id)) {
                    $right = $this->rebuildTree($row->id, $right, $depth + 1);
                    $this->qb
                        ->from($this->table)
                        ->where('id', $row->id)
                        ->update($update = [
                            'record_right' => $right,
                        ]);
                    $update[ 'id' ] = $row->id;
                }

                $i++;
            }
        }

        return $right + 1;
    }

    // ------------------------------------------------------------------------

    /**
     * HierarchicalTrait::getParents
     *
     * @param int    $id
     * @param string $ordering ASC|DESC
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    protected function getParents($id, $ordering = 'ASC')
    {
        if ($result = $this->qb
            ->select($this->table . '.*')
            ->from($this->table)
            ->from($this->table . ' AS node')
            ->whereBetween('node.record_left', [$this->table . '.record_left', $this->table . '.record_right'])
            ->where([
                'node.id' => $id,
            ])
            ->orderBy($this->table . '.record_left', $ordering)
            ->get()) {
            if ($result->count()) {
                return $result;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * HierarchicalTrait::hasParent
     * 
     * @param int $id
     *
     * @return bool
     */
    protected function hasParent($id)
    {
        if ($result = $this->qb
            ->table($this->table)
            ->select('id')
            ->where('id', $id)
            ->get()) {
            return (bool)($result->count() == 0 ? false : true);
        }

        return false;
    }
    
    // ------------------------------------------------------------------------

    /**
     * HierarchicalTrait::getNumParents
     *
     * @param int  $idParent
     * @param bool $direct
     *
     * @return int
     */
    protected function getNumParents($id, $direct = true)
    {
        if($direct) {
            if ($result = $this->qb
                ->table($this->table)
                ->select('id')
                ->where('id', $id)
                ->get()) {
                return $result->count();
            }
        } else {
            if($parents = $this->getParents($id)) {
                return $parents->count();
            }
        }

        return 0;
    }

    // ------------------------------------------------------------------------

    /**
     * HierarchicalTrait::getChilds
     * 
     * @param int    $idParent
     * @param string $ordering ASC|DESC
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    protected function getChilds($idParent, $ordering = 'ASC')
    {
        if ($result = $this->qb
            ->select($this->table . '.*')
            ->from($this->table)
            ->from($this->table . ' AS node')
            ->whereBetween('node.record_left', [$this->table . '.record_left', $this->table . '.record_right'])
            ->where([
                $this->table . '.id' => $id,
            ])
            ->get()) {
            if ($result->count()) {
                return $result;
            }
        }

        return false;
    }
    
    // ------------------------------------------------------------------------

    /**
     * HierarchicalTrait::hasChilds
     *
     * @param int $idParent
     *
     * @return bool
     */
    protected function hasChilds($idParent)
    {
        if ($result = $this->qb
            ->table($this->table)
            ->select('id')
            ->where('id_parent', $idParent)
            ->get()) {
            return (bool)($result->count() == 0 ? false : true);
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * HierarchicalTrait::getNumChilds
     * 
     * @param int  $idParent
     * @param bool $direct
     *
     * @return int
     */
    protected function getNumChilds($idParent, $direct = true)
    {
        if($direct) {
            if ($result = $this->qb
                ->table($this->table)
                ->select('id')
                ->where('id_parent', $idParent)
                ->get()) {
                return $result->count();
            }
        } else {
            if($childs = $this->getChilds($idParent)) {
                return $childs->count();
            }
        }

        return 0;
    }
}