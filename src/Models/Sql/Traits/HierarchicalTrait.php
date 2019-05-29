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
            ->select($this->primaryKey)
            ->where($this->parentKey, $idParent)
            ->orderBy($this->primaryKey)
            ->get()) {

            $right = $left + 1;

            $i = 0;
            foreach ($childs as $child) {
                if ($i == 0) {
                    $this->qb
                        ->from($this->table)
                        ->where($this->primaryKey, $child->id)
                        ->update($update = [
                            'record_left'  => $left,
                            'record_right' => $right,
                            'record_depth' => $depth,
                        ]);
                } else {
                    $this->qb
                        ->from($this->table)
                        ->where($this->primaryKey, $child->{$this->primaryKey})
                        ->update($update = [
                            'record_left'  => $left = $right + 1,
                            'record_right' => $right = $left + 1,
                            'record_depth' => $depth,
                        ]);
                }

                $update[ $this->primaryKey ] = $child->{$this->primaryKey};

                if ($this->qb
                    ->table($this->table)
                    ->select($this->primaryKey)
                    ->where($this->parentKey, $child->{$this->primaryKey})
                    ->orderBy($this->primaryKey)
                    ->get()) {
                    $right = $this->rebuildTree($child->{$this->primaryKey}, $right, $depth + 1);
                    $this->qb
                        ->from($this->table)
                        ->where($this->primaryKey, $child->{$this->primaryKey})
                        ->update($update = [
                            'record_right' => $right,
                        ]);
                    $update[ $this->primaryKey ] = $child->{$this->primaryKey};
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
                'node.' . $this->primaryKey => $id,
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
     * @param int $id
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function getParent($id)
    {
        if ($parent = $this->qb
            ->select($this->table . '.*')
            ->from($this->table)
            ->where($this->primaryKey, $id)
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
     * @param int $idParent
     *
     * @return int
     */
    public function getNumOfParent($id)
    {
        if ($parents = $this->qb
            ->table($this->table)
            ->select($this->primaryKey)
            ->where($this->primaryKey, $id)
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
    public function hasParent($id)
    {
        if ($numOfParents = $this->getNumOfParent($id)) {
            return (bool)($numOfParents == 0 ? false : true);
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * HierarchicalTrait::getNumOfParents
     *
     * @param int $id
     *
     * @return int
     */
    public function getNumOfParents($id)
    {
        if ($parents = $this->getParents($id)) {
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
     * @param int    $id
     * @param string $ordering ASC|DESC
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function getChilds($id, $ordering = 'ASC')
    {
        if ($childs = $this->qb
            ->select($this->table . '.*')
            ->from($this->table)
            ->from($this->table . ' AS node')
            ->whereBetween($this->table . '.record_left', [ 'node.record_left', 'node.record_right'])
            ->where([
                'node.' . $this->primaryKey => $id,
                $this->table . '.' . $this->primaryKey . '!=' => $id,
            ])
            ->orderBy($this->table . '.record_left', $ordering)
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
     * @param int  $id
     *
     * @return int
     */
    public function getNumOfChilds($id)
    {
        if ($childs = $this->getChilds($id)) {
            return $childs->count();
        }

        return 0;
    }

    // ------------------------------------------------------------------------

    /**
     * HierarchicalTrait::hasChilds
     *
     * @param int $id
     *
     * @return bool
     */
    public function hasChilds($id)
    {
        if ($numOfChilds = $this->getNumOfChilds($id)) {
            return (bool)($numOfChilds == 0 ? false : true);
        }

        return false;
    }
}