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
 * Class AdjacencyTrait
 *
 * @package O2System\Framework\Models\Sql\Traits
 */
trait AdjacencyTrait
{
    /**
     * AdjacencyTrait::$parentKey
     *
     * @var string
     */
    public $parentKey = 'id_parent';

    /**
     * AdjacencyTrait::$adjacency
     *
     * @var bool
     */
    protected $adjacency = true;

    // ------------------------------------------------------------------------

    /**
     * AdjacencyTrait::getParent
     *
     * @param int $id
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function getParent($id)
    {
        if ($parent = $this->qb
            ->from($this->table)
            ->where($this->parentKey, $id)
            ->get(1)) {
            if ($parent->count() == 1) {
                return $parent;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * AdjacencyTrait::getNumOfParent
     *
     * @param int $id
     *
     * @return int
     */
    public function getNumOfParent($id)
    {
        if ($parents = $this->qb
            ->table($this->table)
            ->select('id')
            ->where('id', $id)
            ->get(1)) {
            return $parents->count();
        }

        return 0;
    }

    // ------------------------------------------------------------------------

    /**
     * AdjacencyTrait::hasParent
     *
     * @param int $id
     *
     * @return bool
     */
    public function hasParent($id)
    {
        if (($numParents = $this->getNumOfParent($id)) > 0) {
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * AdjacencyTrait::getChilds
     *
     * @param int $id
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function getChilds($id)
    {
        if ($childs = $this->qb
            ->from($this->table)
            ->where($this->parentKey, $id)
            ->get()) {
            if ($childs->count() > 0) {
                return $childs;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * AdjacencyTrait::getNumChilds
     *
     * @param int $id
     *
     * @return bool
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
     * AdjacencyTrait::hasChilds
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