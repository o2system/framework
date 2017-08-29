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
    protected $isUseAdjacency = true;

    /**
     * Get Children
     *
     * @param int         $id_parent
     * @param string|null $table
     *
     * @access  public
     * @return  mixed
     */
    public function getChildren( $id_parent, $table = null )
    {
        $table = isset( $table ) ? $table : $this->table;

        $result = $this->db->getWhere( $table, [ $this->parentKey => $id_parent ] );

        if ( $result->count() > 0 ) {
            return $result;
        }

        return false;
    }

    /**
     * Has Children
     *
     * @param int         $id_parent
     * @param string|null $table
     *
     * @access  public
     * @return  bool
     */
    public function hasChildren( $id_parent, $table = null )
    {
        $table = isset( $table ) ? $table : $this->table;

        $result = $this->db->select( 'id' )->getWhere( $table, [ $this->parentKey => $id_parent ] );

        if ( $result->count() > 0 ) {
            return true;
        }

        return false;
    }
}