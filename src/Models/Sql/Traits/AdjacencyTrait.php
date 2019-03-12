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
     * AdjacencyTrait::getChilds
     *
     * @param int $idParent
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function getChilds($idParent)
    {
        if ($result = $this->qb
            ->from($this->table)
            ->where([$this->parentKey => $idParent])
            ->get()) {
            if ($result->count() > 0) {
                return $result;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * AdjacencyTrait
     *
     * @param int $idParent
     *
     * @return bool
     */
    public function hasChilds($idParent)
    {
        if ($result = $this->qb
            ->select('id')
            ->from($this->table)
            ->where([$this->parentKey => $idParent])
            ->get()) {
            if ($result->count() > 0) {
                return true;
            }
        }

        return false;
    }
}