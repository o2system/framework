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
 * Class TraitModifier
 *
 * @package O2System\Framework\Models\Sql\Traits
 */
trait ModifierTrait
{
    /**
     * Insert Data
     *
     * Method to input data as well as equipping the data in accordance with the fields
     * in the destination database table.
     *
     * @access  public
     *
     * @param   array  $sets  Array of Input Data
     *
     * @return mixed
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    public function insert(array $sets)
    {
        if (method_exists($this, 'insertRecordSets')) {
            $this->insertRecordSets($sets);
        }

        if (method_exists($this, 'beforeInsert')) {
            $this->beforeInsert($sets);
        }

        if (method_exists($this, 'getRecordOrdering')) {
            if ($this->recordOrdering === true && empty($sets[ 'record_ordering' ])) {
                $sets[ 'record_ordering' ] = $this->getRecordOrdering($this->table);
            }
        }

        if ($this->qb->table($this->table)->insert($sets)) {
            if (method_exists($this, 'afterInsert')) {
                return $this->afterInsert();
            }

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    public function insertOrUpdate(array $sets)
    {
        // Try to find
        if($result = $this->qb->from($this->table)->getWhere($sets)) {
            return $this->update($sets);
        } else {
            return $this->insert($sets);
        }

        return false;
    }

    public function insertMany(array $sets)
    {
        if (method_exists($this, 'insertRecordSets')) {
            foreach ($sets as $set) {
                $this->insertRecordSets($set);

                if ($this->recordOrdering === true && empty($sets[ 'record_ordering' ])) {
                    $set[ 'record_ordering' ] = $this->getRecordOrdering($this->table);
                }
            }
        }

        if (method_exists($this, 'beforeInsertMany')) {
            $this->beforeInsertMany($sets);
        }

        if ($this->qb->table($this->table)->insertBatch($sets)) {
            if (method_exists($this, 'afterInsertMany')) {
                return $this->afterInsertMany();
            }

            return true;
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
     *
     * @param   array  $sets  Array of Update Data
     *
     * @return mixed
     */
    public function update(array $sets, $where = [])
    {
        $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';

        if (empty($where)) {
            if (empty($this->primaryKeys)) {
                $where[ $primaryKey ] = $sets[ $primaryKey ];
            } else {
                foreach ($this->primaryKeys as $primaryKey) {
                    $where[ $primaryKey ] = $sets[ $primaryKey ];
                }
            }
        }

        // Reset Primary Keys
        $this->primaryKey = 'id';
        $this->primaryKeys = [];

        if (method_exists($this, 'updateRecordSets')) {
            $this->updateRecordSets($sets);
        }

        if (method_exists($this, 'beforeUpdate')) {
            $sets = $this->beforeUpdate($sets);
        }

        if (method_exists($this, 'getRecordOrdering')) {
            if ($this->recordOrdering === true && empty($sets[ 'record_ordering' ])) {
                $sets[ 'record_ordering' ] = $this->getRecordOrdering($this->table);
            }
        }

        if ($this->qb->table($this->table)->update($sets, $where)) {

            if (method_exists($this, 'afterUpdate')) {
                return $this->afterUpdate();
            }

            return true;
        }

        return false;
    }

    /**
     * Find Or Insert
     *
     * To insert if there is no record before. 
     * This is very important in insert to pivot table and avoid redundan
     * 
     * @access public
     * @param array  $sets Array of Input Data
     * @return int
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    public function findOrInsert(array $sets)
    {
        $result = $this->qb->from($this->table)->getWhere($sets);
        
        if ($result->count() == 0) {
            $this->insert($sets);
            
            return $this->db->getLastInsertId();
        }

        return $result[0]->id;
    }

    public function updateOrInsert(array $sets, array $where = [])
    {
        $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';

        if (empty($where)) {
            if (empty($this->primaryKeys)) {
                $where[ $primaryKey ] = $sets[ $primaryKey ];
            } else {
                foreach ($this->primaryKeys as $primaryKey) {
                    $where[ $primaryKey ] = $sets[ $primaryKey ];
                }
            }
        }

        // Reset Primary Keys
        $this->primaryKey = 'id';
        $this->primaryKeys = [];

        // Try to find
        if($result = $this->qb->from($this->table)->getWhere($where)) {
            return $this->update($sets, $where);
        } else {
            return $this->insert($sets);
        }

        return false;
    }

    // ------------------------------------------------------------------------

    public function updateMany(array $sets)
    {
        $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';

        $where = [];
        if (empty($this->primaryKeys)) {
            $where[ $primaryKey ] = $sets[ $primaryKey ];
            $this->qb->where($primaryKey, $sets[ $primaryKey ]);
        } else {
            foreach ($this->primaryKeys as $primaryKey) {
                $where[ $primaryKey ] = $sets[ $primaryKey ];
            }
        }

        // Reset Primary Keys
        $this->primaryKey = 'id';
        $this->primaryKeys = [];

        if (method_exists($this, 'updateRecordSets')) {
            foreach ($sets as $set) {
                $this->updateRecordSets($set);

                if ($this->recordOrdering === true && empty($sets[ 'record_ordering' ])) {
                    $set[ 'record_ordering' ] = $this->getRecordOrdering($this->table);
                }
            }
        }

        if (method_exists($this, 'beforeUpdateMany')) {
            $this->beforeUpdateMany($sets);
        }

        if ($this->qb->table($this->table)->updateBatch($sets, $where)) {
            if (method_exists($this, 'afterUpdateMany')) {
                return $this->afterUpdateMany();
            }

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * trash
     *
     * @param      $id
     *
     * @return array|bool
     */
    public function trash($id)
    {
        $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';

        $sets = [];
        $where = [];

        if (empty($this->primaryKeys)) {
            $where[ $primaryKey ] = $id;
            $sets[ $primaryKey ] = $id;
        } elseif (is_array($id)) {
            foreach ($this->primaryKeys as $primaryKey) {
                $where[ $primaryKey ] = $sets[ $primaryKey ];
                $sets[ $primaryKey ] = $id[ $primaryKey ];
            }
        } else {
            foreach ($this->primaryKeys as $primaryKey) {
                $where[ $primaryKey ] = $sets[ $primaryKey ];
            }

            $sets[ reset($this->primaryKeys) ] = $id;
        }

        // Reset Primary Keys
        $this->primaryKey = 'id';
        $this->primaryKeys = [];

        if (method_exists($this, 'updateRecordSets')) {
            $this->setRecordStatus('TRASH');
            $this->updateRecordSets($sets);
        }

        if (method_exists($this, 'beforeTrash')) {
            $this->beforeTrash($sets);
        }

        if ($this->qb->table($this->table)->update($sets, $where)) {
            if (method_exists($this, 'afterTrash')) {
                return $this->afterTrash();
            }

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    public function trashBy($id, array $where = [])
    {
        $this->qb->where($where);

        return $this->trash($id);
    }

    // ------------------------------------------------------------------------

    /**
     * Trash many rows from the database table based on sets of ids.
     *
     * @param array $ids
     *
     * @return mixed
     */
    public function trashMany(array $ids)
    {
        $affectedRows = [];

        foreach ($ids as $id) {
            $affectedRows[ $id ] = $this->trash($id);
        }

        return $affectedRows;
    }

    // ------------------------------------------------------------------------

    public function trashManyBy(array $ids, $where = [])
    {
        $affectedRows = [];

        foreach ($ids as $id) {
            $affectedRows[ $id ] = $this->trashBy($id, $where);
        }

        return $affectedRows;
    }

    // ------------------------------------------------------------------------

    public function delete($id)
    {
        $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';

        $where = [];
        if (empty($this->primaryKeys)) {
            $where[ $primaryKey ] = $id;
        } elseif (is_array($id)) {
            foreach ($this->primaryKeys as $primaryKey) {
                $where[ $primaryKey ] = $id[ $primaryKey ];
            }
        } else {
            $where[ reset($this->primaryKeys) ] = $id;
        }

        // Reset Primary Keys
        $this->primaryKey = 'id';
        $this->primaryKeys = [];

        if (method_exists($this, 'beforeDelete')) {
            $this->beforeDelete();
        }

        if ($this->qb->table($this->table)->delete($where)) {
            if (method_exists($this, 'afterDelete')) {
                return $this->afterDelete();
            }

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    public function deleteBy($id, $where = [], $force = false)
    {
        $this->qb->where($where);

        return $this->delete($id, $force);
    }

    // ------------------------------------------------------------------------

    public function deleteMany(array $ids, $force = false)
    {
        $affectedRows = [];

        foreach ($ids as $id) {
            $affectedRows[ $id ] = $this->delete($id, $force);
        }

        return $affectedRows;
    }

    public function deleteManyBy(array $ids, $where = [], $force = false)
    {
        $affectedRows = [];

        foreach ($ids as $id) {
            $affectedRows[ $id ] = $this->deleteBy($id, $where, $force);
        }

        return $affectedRows;
    }

    // ------------------------------------------------------------------------

    public function publish($id)
    {
        $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';

        $sets = [];
        $where = [];

        if (empty($this->primaryKeys)) {
            $where[ $primaryKey ] = $id;
            $sets[ $primaryKey ] = $id;
        } elseif (is_array($id)) {
            foreach ($this->primaryKeys as $primaryKey) {
                $where[ $primaryKey ] = $sets[ $primaryKey ];
                $sets[ $primaryKey ] = $id[ $primaryKey ];
            }
        } else {
            foreach ($this->primaryKeys as $primaryKey) {
                $where[ $primaryKey ] = $sets[ $primaryKey ];
            }

            $sets[ reset($this->primaryKeys) ] = $id;
        }

        // Reset Primary Keys
        $this->primaryKey = 'id';
        $this->primaryKeys = [];

        if (method_exists($this, 'updateRecordSets')) {
            $this->setRecordStatus('PUBLISH');
            $this->updateRecordSets($sets);
        }

        if (method_exists($this, 'beforePublish')) {
            $this->beforePublish($sets);
        }

        if ($this->qb->table($this->table)->update($sets, $where)) {
            if (method_exists($this, 'afterPublish')) {
                return $this->afterPublish();
            }

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    public function publishBy($id, array $where = [])
    {
        $this->qb->where($where);

        return $this->publish($id);
    }

    // ------------------------------------------------------------------------

    public function publishMany(array $ids)
    {
        $affectedRows = [];

        foreach ($ids as $id) {
            $affectedRows[ $id ] = $this->publish($id);
        }

        return $affectedRows;
    }

    // ------------------------------------------------------------------------

    public function publishManyBy(array $ids, $where = [])
    {
        $affectedRows = [];

        foreach ($ids as $id) {
            $affectedRows[ $id ] = $this->publishBy($id, $where);
        }

        return $affectedRows;
    }

    // ------------------------------------------------------------------------

    public function restore($id)
    {
        return $this->publish($id);
    }

    // ------------------------------------------------------------------------

    public function restoreBy($id, array $where = [])
    {
        return $this->publishBy($id, $where);
    }

    // ------------------------------------------------------------------------

    public function restoreMany(array $ids)
    {
        return $this->publishMany($ids);
    }

    // ------------------------------------------------------------------------

    public function restoreManyBy(array $ids, $where = [])
    {
        return $this->publishManyBy($ids, $where);
    }
}