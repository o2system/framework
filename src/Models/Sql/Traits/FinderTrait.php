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

use O2System\Database\DataObjects\Result;
use O2System\Framework\Models\Sql\DataObjects;

/**
 * Class FinderTrait
 *
 * @package O2System\Framework\Models\Sql\Traits
 */
trait FinderTrait
{
    /**
     * FinderTrait::all
     * 
     * @param null $fields
     * @param null $limit
     * @return bool|DataObjects\Result
     */
    public function all( $fields = null, $limit = null )
    {
        if ( isset( $fields ) ) {
            $this->db->select( $fields );
        }

        if( isset( $limit ) ) {
            $this->db->limit( $limit );
        }
        
        $result = $this->db->from( $this->table )->get();

        if ( $result->count() > 0 ) {
            $this->result = new DataObjects\Result( $result, $this );

            return $this->result;
        }

        return false;
    }
    // ------------------------------------------------------------------------

    /**
     * Find
     *
     * Find single or many record base on criteria by specific field
     *
     * @param   string      $criteria Criteria value
     * @param   string|null $field    Table column field name | set to primary key by default
     *
     * @return  DataObjects\Result|DataObjects\Result\Row|bool Returns FALSE if failed.
     */
    public function find( $criteria, $field = null, $limit = null )
    {
        if ( is_array( $criteria ) ) {
            return $this->findIn( $criteria, $field );
        }

        $field = isset( $field ) ? $field : $this->primaryKey;

        $result = $this->db
            ->from( $this->table )
            ->getWhere( [ $field => $criteria ], $limit );

        if( $result instanceof Result ) {
            if ( $result->count() > 0 ) {
                $this->result = new DataObjects\Result( $result, $this );

                if( $this->result->count() == 1 ) {
                    return $this->result->first();
                }

                return $this->result;
            }
        }

        return false;
    }
    // ------------------------------------------------------------------------

    /**
     * Find In
     *
     * Find many records within criteria on specific field
     *
     * @param   array  $inCriteria List of criteria
     * @param   string $field      Table column field name | set to primary key by default
     *
     * @return  DataObjects\Result|bool Returns FALSE if failed.
     */
    public function findIn( array $inCriteria, $field = null )
    {
        $field = isset( $field ) ? $field : $this->primaryKey;

        $result = $this->db
            ->from( $this->table )
            ->whereIn( $field, $inCriteria )
            ->get();

        if ( $result->count() > 0 ) {
            $this->result = new DataObjects\Result( $result, $this );

            return $this->result;
        }

        return false;
    }
    // ------------------------------------------------------------------------

    /**
     * Find By
     *
     * Find single record based on certain conditions
     *
     * @param   array $conditions List of conditions with criteria
     *
     * @access  protected
     * @return  DataObjects\Result|bool Returns FALSE if failed.
     */
    public function findWhere( array $conditions, $limit = null )
    {
        $result = $this->db
            ->from( $this->table )
            ->getWhere( $conditions, $limit );

        if ( $result->count() > 0 ) {
            $this->result = new DataObjects\Result( $result, $this );

            if( $limit == 1 ) {
                return $this->result->first();
            }

            return $this->result;
        }

        return false;
    }
    // ------------------------------------------------------------------------

    /**
     * Find In
     *
     * Find many records not within criteria on specific field
     *
     * @param   array  $notInCriteria List of criteria
     * @param   string $field         Table column field name | set to primary key by default
     *
     * @return  DataObjects\Result|bool Returns FALSE if failed.
     */
    public function findNotIn( array $notInCriteria, $field = null )
    {
        $field = isset( $field ) ? $field : $this->primaryKey;

        $result = $this->db
            ->from( $this->table )
            ->whereNotIn( $field, $notInCriteria )
            ->get();

        if ( $result->count() > 0 ) {
            $this->result = new DataObjects\Result( $result, $this );

            return $this->result;
        }

        return false;
    }
}