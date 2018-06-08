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
 * Class AdjacencyTrait
 *
 * @package O2System\Framework\Models\Sql\Traits
 */
trait AdjacencyTrait
{
    /**
     * Parent Key Field
     *
     * @access  public
     * @type    string
     */
    public $parentKey = 'id_parent';

    /**
     * Adjacency Enabled Flag
     *
     * @access  protected
     * @type    bool
     */
    protected $adjacency = true;

    /**
     * Get Children
     *
     * @param int         $idParent
     * @param string|null $table
     *
     * @access  public
     * @return  mixed
     */
    public function getChildren($idParent, $table = null)
    {
        $table = isset($table) ? $table : $this->table;

        $result = $this->qb
            ->from($table)
            ->getWhere([$this->parentKey => $idParent]);

        if ($result->count() > 0) {
            return $result;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Has Children
     *
     * @param int         $idParent
     * @param string|null $table
     *
     * @access  public
     * @return  bool
     */
    public function hasChildren($idParent, $table = null)
    {
        $table = isset($table) ? $table : $this->table;

        $result = $this->qb
            ->select('id')
            ->from($table)
            ->getWhere([$this->parentKey => $idParent]);

        if ($result->count() > 0) {
            return true;
        }

        return false;
    }
}