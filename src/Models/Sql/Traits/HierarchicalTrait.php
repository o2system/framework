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
    public function rebuildTree($idParent = 0, $left = 1, $depth = 0)
    {
        ini_set('xdebug.max_nesting_level', 10000);
        ini_set('memory_limit', '-1');

        if ($childs = $this->qb
            ->table($this->table)
            ->select('id')
            ->where($this->parentKey, $idParent)
            ->orderBy('id')
            ->get()) {

            $right = $left + 1;

            $i = 0;
            foreach ($childs as $child) {
                if ($i == 0) {
                    $this->qb
                        ->from($this->table)
                        ->where('id', $child->id)
                        ->update($update = [
                            'record_left'  => $left,
                            'record_right' => $right,
                            'record_depth' => $depth,
                        ]);
                } else {
                    $this->qb
                        ->from($this->table)
                        ->where('id', $child->id)
                        ->update($update = [
                            'record_left'  => $left = $right + 1,
                            'record_right' => $right = $left + 1,
                            'record_depth' => $depth,
                        ]);
                }

                $update[ 'id' ] = $child->id;

                if ($this->hasChilds($child->id)) {
                    $right = $this->rebuildTree($child->id, $right, $depth + 1);
                    $this->qb
                        ->from($this->table)
                        ->where('id', $child->id)
                        ->update($update = [
                            'record_right' => $right,
                        ]);
                    $update[ 'id' ] = $child->id;
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
    public function getParents($id, $ordering = 'ASC')
    {
        if ($parents = $this->qb
            ->select($this->table . '.*')
            ->from($this->table)
            ->from($this->table . ' AS node')
            ->whereBetween('node.record_left', [$this->table . '.record_left', $this->table . '.record_right'])
            ->where([
                'node.id' => $id,
            ])
            ->orderBy($this->table . '.record_left', $ordering)
            ->get()) {
            if ($parents->count()) {
                return $parents;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * HierarchicalTrait::getParent
     *
     * @param int $idParent
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function getParent($idParent)
    {
        if ($parent = $this->qb
            ->select($this->table . '.*')
            ->from($this->table)
            ->where($this->primaryKey, $idParent)
            ->get(1)) {
            if ($parent->count() == 1) {
                return $parent->first();
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * HierarchicalTrait::getNumOfParent
     *
     * @param int  $idParent
     *
     * @return int
     */
    public function getNumOfParent($id)
    {
        if ($parents = $this->qb
            ->table($this->table)
            ->select('id')
            ->where('id', $id)
            ->get()) {
            return $parents->count();
        }

        return 0;
    }

    // ------------------------------------------------------------------------

    /**
     * HierarchicalTrait::hasParent
     *
     * @param int $id
     *
     * @return bool
     */
    public function hasParent($id, $direct = true)
    {
        if ($numOfParents = $this->getNumOfParent($idParent, $direct)) {
            return (bool)($numOfParents == 0 ? false : true);
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * HierarchicalTrait::getNumOfParents
     *
     * @param int  $id
     *
     * @return int
     */
    public function getNumOfParents($id)
    {
        if($parents = $this->getParents($id)) {
            return $parents->count();
        }

        return 0;
    }

    // ------------------------------------------------------------------------

    /**
     * HierarchicalTrait::hasParents
     *
     * @param int $id
     *
     * @return bool
     */
    public function hasParents($id)
    {
        if ($numOfParents = $this->getNumOfParents($id)) {
            return (bool)($numOfParents == 0 ? false : true);
        }

        return false;
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
    public function getChilds($idParent, $ordering = 'ASC')
    {
        if ($childs = $this->qb
            ->select($this->table . '.*')
            ->from($this->table)
            ->from($this->table . ' AS node')
            ->whereBetween('node.record_left', [$this->table . '.record_left', $this->table . '.record_right'])
            ->where([
                $this->table . '.id' => $id,
            ])
            ->get()) {
            if ($childs->count()) {
                return $childs;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * HierarchicalTrait::getNumOfChilds
     *
     * @param int  $idParent
     * @param bool $direct
     *
     * @return int
     */
    public function getNumOfChilds($idParent, $direct = true)
    {
        if($childs = $this->getChilds($idParent)) {
            return $childs->count();
        }

        return 0;
    }

    // ------------------------------------------------------------------------

    /**
     * HierarchicalTrait::hasChilds
     *
     * @param int $idParent
     *
     * @return bool
     */
    public function hasChilds($idParent)
    {
        if ($numOfChilds = $this->getNumOfChilds($idParent)) {
            return (bool)($numOfChilds == 0 ? false : true);
        }

        return false;
    }
}