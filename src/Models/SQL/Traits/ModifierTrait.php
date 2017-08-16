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

namespace O2System\Framework\Models\SQL\Traits;

// ------------------------------------------------------------------------

/**
 * Class TraitModifier
 *
 * @package O2System\Framework\Models\SQL\Traits
 */
trait ModifierTrait
{
    use BeforeAfterTrait;

    /**
     * Insert Data
     *
     * Method to input data as well as equipping the data in accordance with the fields
     * in the destination database table.
     *
     * @access  public
     * @final   This method cannot be overwritten
     *
     * @param   array  $sets  Array of Input Data
     * @param   string $table Table Name
     *
     * @return mixed
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    final public function insert( array $sets, $table = null )
    {
        $table = isset( $table ) ? $table : $this->table;

        $sets = $this->beforeProcess( $sets, $table );

        if ( $this->db->table( $table )->insert( $sets ) ) {
            if ( empty( $this->afterProcess ) ) {
                return true;
            }

            return $this->afterProcess();
        }

        return false;
    }

    /**
     * Update Data
     *
     * Method to update data as well as equipping the data in accordance with the fields
     * in the destination database table.
     *
     * @access  public
     * @final   This method cannot be overwritten
     *
     * @param   string $table Table Name
     * @param   array  $sets  Array of Update Data
     *
     * @return mixed
     */
    final public function update( array $sets, $table = null )
    {
        $table = isset( $table ) ? $table : $this->table;
        $primaryKey = isset( $this->primaryKey ) ? $this->primaryKey : 'id';

        $where = [];
        if ( empty( $this->primaryKeys ) ) {
            $where[ $primaryKey ] = $sets[ $primaryKey ];
            $this->db->where( $primaryKey, $sets[ $primaryKey ] );
        } else {
            foreach ( $this->primaryKeys as $primaryKey ) {
                $where[ $primaryKey ] = $sets[ $primaryKey ];
            }
        }

        // Reset Primary Keys
        $this->primaryKey = 'id';
        $this->primaryKeys = [];

        $sets = $this->beforeProcess( $sets, $table );

        if ( $this->db->table( $table )->update( $sets, $where ) ) {

            if ( empty( $this->afterProcess ) ) {
                return true;
            }

            return $this->afterProcess();
        }

        return false;
    }

    protected function insertMany( array $sets )
    {
        $table = isset( $table ) ? $table : $this->table;

        $sets = $this->beforeProcess( $sets, $table );

        if ( $this->db->table( $table )->insertBatch( $sets ) ) {
            if ( empty( $this->afterProcess ) ) {
                return true;
            }

            return $this->afterProcess();
        }

        return false;
    }

    // ------------------------------------------------------------------------

    protected function updateMany( array $sets )
    {
        $table = isset( $table ) ? $table : $this->table;
        $primaryKey = isset( $this->primaryKey ) ? $this->primaryKey : 'id';

        $where = [];
        if ( empty( $this->primaryKeys ) ) {
            $where[ $primaryKey ] = $sets[ $primaryKey ];
            $this->db->where( $primaryKey, $sets[ $primaryKey ] );
        } else {
            foreach ( $this->primaryKeys as $primaryKey ) {
                $where[ $primaryKey ] = $sets[ $primaryKey ];
            }
        }

        // Reset Primary Keys
        $this->primaryKey = 'id';
        $this->primaryKeys = [];

        $sets = $this->beforeProcess( $sets, $table );

        if ( $this->db->table( $table )->updateBatch( $sets, $where ) ) {

            if ( empty( $this->afterProcess ) ) {
                return true;
            }

            return $this->afterProcess();
        }

        return false;
    }

    /**
     * Trash many rows from the database table based on sets of ids.
     *
     * @param array $ids
     *
     * @return mixed
     */
    protected function trashMany( array $ids )
    {
        $affectedRows = [];

        foreach ( $ids as $id ) {
            $affectedRows[ $id ] = $this->trash( $id );
        }

        return $affectedRows;
    }

    // ------------------------------------------------------------------------

    /**
     * trash
     *
     * @param      $id
     * @param null $table
     *
     * @return array|bool
     */
    final public function trash( $id, $table = null )
    {
        $table = isset( $table ) ? $table : $this->table;
        $primaryKey = isset( $this->primaryKey ) ? $this->primaryKey : 'id';

        $sets[ 'record_status' ] = 'DELETE';
        $where = [];

        if ( empty( $this->primaryKeys ) ) {
            $where[ $primaryKey ] = $id;
            $sets[ $primaryKey ] = $id;
        } elseif ( is_array( $id ) ) {
            foreach ( $this->primaryKeys as $primaryKey ) {
                $where[ $primaryKey ] = $sets[ $primaryKey ];
                $sets[ $primaryKey ] = $id[ $primaryKey ];
            }
        } else {
            foreach ( $this->primaryKeys as $primaryKey ) {
                $where[ $primaryKey ] = $sets[ $primaryKey ];
            }

            $sets[ reset( $this->primaryKeys ) ] = $id;
        }

        // Reset Primary Keys
        $this->primaryKey = 'id';
        $this->primaryKeys = [];

        $sets = $this->beforeProcess( $sets, $table );

        if ( $this->db->table( $table )->update( $sets ) ) {
            if ( empty( $this->afterProcess ) ) {
                return true;
            }

            return $this->afterProcess();
        }

        return false;
    }

    // ------------------------------------------------------------------------

    protected function trashManyBy( array $ids, $where = [], $table = null )
    {
        $affectedRows = [];

        foreach ( $ids as $id ) {
            $affectedRows[ $id ] = $this->trashBy( $id, $where, $table );
        }

        return $affectedRows;
    }

    protected function trashBy( $id, array $where = [], $table = null )
    {
        $this->db->where( $where );

        return $this->trash( $id, $table );
    }

    protected function deleteMany( array $ids, $force = false, $table = null )
    {
        $affectedRows = [];

        foreach ( $ids as $id ) {
            $affectedRows[ $id ] = $this->delete( $id, $force, $table );
        }

        return $affectedRows;
    }

    public function delete( $id, $force = false, $table = null )
    {
        if ( ( isset( $table ) AND is_bool( $table ) ) OR ! isset( $table ) ) {
            $table = $this->table;
        }

        if ( isset( $this->adjacencyEnabled ) ) {
            if ( $this->hasChildren( $id, $table ) ) {
                if ( $force === true ) {
                    if ( $childrens = $this->getChildren( $id, $table ) ) {
                        foreach ( $childrens as $children ) {
                            $report[ $children->id ] = $this->delete( $children->id, $force, $table );
                        }
                    }
                }
            }
        }

        // Recursive Search File
        $fields = [ 'file', 'document', 'image', 'picture', 'cover', 'avatar', 'photo', 'video' ];

        foreach ( $fields as $field ) {
            if ( $this->db->isTableFieldExists( $field, $table ) ) {
                $primaryKey = isset( $this->primaryKey ) ? $this->primaryKey : 'id';

                if ( empty( $this->primaryKeys ) ) {
                    $this->db->where( $primaryKey, $id );
                } elseif ( is_array( $id ) ) {
                    foreach ( $this->primaryKeys as $primaryKey ) {
                        $this->db->where( $primaryKey, $id[ $primaryKey ] );
                    }
                } else {
                    $this->db->where( reset( $this->primaryKeys ), $id );
                }

                $result = $this->db->select( $field )->limit( 1 )->get( $table );

                if ( $result->count() > 0 ) {
                    if ( ! empty( $result->first()->{$field} ) ) {
                        $directory = new \RecursiveDirectoryIterator( PATH_STORAGE );
                        $iterator = new \RecursiveIteratorIterator( $directory );
                        $results = new \RegexIterator(
                            $iterator,
                            '/' . $result->first()->{$field} . '/i',
                            \RecursiveRegexIterator::GET_MATCH
                        );

                        foreach ( $results as $file ) {
                            if ( is_array( $file ) ) {
                                foreach ( $file as $filepath ) {
                                    @unlink( $filepath );
                                }
                            }
                        }
                    }
                }
            }
        }

        $primaryKey = isset( $this->primaryKey ) ? $this->primaryKey : 'id';

        $where = [];
        if ( empty( $this->primaryKeys ) ) {
            $where[ $primaryKey ] = $id;
        } elseif ( is_array( $id ) ) {
            foreach ( $this->primaryKeys as $primaryKey ) {
                $where[ $primaryKey ] = $id[ $primaryKey ];
            }
        } else {
            $where[ reset( $this->primaryKeys ) ] = $id;
        }

        // Reset Primary Keys
        $this->primaryKey = 'id';
        $this->primaryKeys = [];

        if ( $this->db->table( $table )->delete( $where ) ) {
            if ( empty( $this->afterProcess ) ) {
                return true;
            }

            return $this->afterProcess();
        }

        return false;
    }

    protected function deleteManyBy( array $ids, $where = [], $force = false, $table = null )
    {
        $affectedRows = [];

        foreach ( $ids as $id ) {
            $affectedRows[ $id ] = $this->deleteBy( $id, $where, $force, $table );
        }

        return $affectedRows;
    }

    protected function deleteBy( $id, $where = [], $force = false, $table = null )
    {
        $this->db->where( $where );

        return $this->delete( $id, $force, $table );
    }
}