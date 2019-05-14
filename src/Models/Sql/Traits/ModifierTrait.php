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
 * Class TraitModifier
 *
 * @package O2System\Framework\Models\Sql\Traits
 */
trait ModifierTrait
{
    /**
     * ModifierTrait::insert
     *
     * @param array $sets
     *
     * @return bool
     */
    public function insert(array $sets)
    {
        if (count($sets)) {
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
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::insertOrUpdate
     *
     * @param array $sets
     *
     * @return bool
     */
    public function insertOrUpdate(array $sets)
    {
        if ($result = $this->qb->from($this->table)->getWhere($sets)) {
            if ($result->count() == 1) {
                return $this->update($sets);
            } else {
                return $this->insert($sets);
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::insertMany
     *
     * @param array $sets
     *
     * @return bool|int
     */
    public function insertMany(array $sets)
    {
        if (count($sets)) {
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

                return $this->db->getAffectedRows();
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::insertIfNotExists
     *
     * @param array $sets
     * @param array $conditions
     *
     * @return bool
     */
    public function insertIfNotExists(array $sets, array $conditions = [])
    {
        if (count($sets)) {
            if ($result = $this->qb->from($this->table)->getWhere($conditions)) {
                if ($result->count() == 0) {
                    return $this->insert($sets);
                }
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::update
     *
     * @param array $sets
     * @param array $conditions
     *
     * @return bool
     */
    public function update(array $sets, $conditions = [])
    {
        if (count($sets)) {
            $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';

            if (empty($conditions)) {
                if (isset($sets[ $primaryKey ])) {
                    $conditions = [$primaryKey => $sets[ $primaryKey ]];
                }
            }

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

            if ($this->qb->table($this->table)->update($sets, $conditions)) {

                if (method_exists($this, 'afterUpdate')) {
                    return $this->afterUpdate();
                }

                return true;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::updateOrInsert
     *
     * @param array $sets
     * @param array $conditions
     *
     * @return bool
     */
    public function updateOrInsert(array $sets, array $conditions = [])
    {
        if (count($sets)) {
            $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';

            if (empty($conditions)) {
                if (isset($sets[ $primaryKey ])) {
                    $conditions = [$primaryKey => $sets[ $primaryKey ]];
                } else {
                    $conditions = $sets;
                }
            }

            // Try to find
            if ($result = $this->qb->from($this->table)->getWhere($conditions)) {
                return $this->update($sets, $conditions);
            } else {
                return $this->insert($sets);
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::updateMany
     *
     * @param array $sets
     *
     * @return bool|int
     */
    public function updateMany(array $sets)
    {
        $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';

        if (method_exists($this, 'updateRecordSets')) {
            foreach ($sets as $key => $set) {
                $this->updateRecordSets($sets[ $key ]);
            }
        }

        if (method_exists($this, 'beforeUpdateMany')) {
            $this->beforeUpdateMany($sets);
        }

        if ($this->qb->table($this->table)->updateBatch($sets, $primaryKey)) {
            if (method_exists($this, 'afterUpdateMany')) {
                return $this->afterUpdateMany();
            }

            return $this->db->getAffectedRows();
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::delete
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete($id)
    {
        $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';

        if (method_exists($this, 'beforeDelete')) {
            $this->beforeDelete();
        }

        if ($this->qb->table($this->table)->limit(1)->delete([$primaryKey => $id])) {
            if (method_exists($this, 'afterDelete')) {
                return $this->afterDelete();
            }

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::deleteBy
     *
     * @param array $conditions
     *
     * @return bool
     */
    public function deleteBy($conditions = [])
    {
        if (count($conditions)) {
            if (method_exists($this, 'beforeDelete')) {
                $this->beforeDelete();
            }

            if (method_exists($this, 'beforeDelete')) {
                $this->beforeDelete();
            }

            if ($this->qb->table($this->table)->limit(1)->delete($conditions)) {
                if (method_exists($this, 'afterDelete')) {
                    return $this->afterDelete();
                }

                return $this->db->getAffectedRows();
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::deleteMany
     *
     * @param array $ids
     *
     * @return bool|int
     */
    public function deleteMany(array $ids)
    {
        $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';

        if (method_exists($this, 'beforeDelete')) {
            $this->beforeDelete();
        }

        $this->qb->whereIn($primaryKey, $ids);

        if ($this->qb->table($this->table)->delete()) {
            if (method_exists($this, 'afterDelete')) {
                return $this->afterDelete();
            }

            return $this->db->getAffectedRows();
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::deleteManyBy
     *
     * @param array $conditions
     *
     * @return bool|int
     */
    public function deleteManyBy($conditions = [])
    {
        if (count($conditions)) {
            if (method_exists($this, 'beforeDelete')) {
                $this->beforeDelete();
            }

            if ($this->qb->table($this->table)->delete($conditions)) {
                if (method_exists($this, 'afterDelete')) {
                    return $this->afterDelete();
                }

                return $this->db->getAffectedRows();
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::updateRecordStatus
     *
     * @param int $id
     *
     * @return bool
     */
    private function updateRecordStatus($id, $recordStatus, $method)
    {
        $sets[ 'record_status' ] = $recordStatus;
        $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';

        if (method_exists($this, 'updateRecordSets')) {
            $this->updateRecordSets($sets);
        }

        if (method_exists($this, $beforeMethod = 'before' . ucfirst($method))) {
            call_user_func_array([&$this, $beforeMethod], [$sets]);
        }

        if ($this->qb->table($this->table)->limit(1)->update($sets, [$primaryKey => $id])) {
            if (method_exists($this, $afterMethod = 'after' . ucfirst($method))) {
                return call_user_func([&$this, $beforeMethod]);
            }

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::updateRecordStatusMany
     *
     * @param array $ids
     *
     * @return bool|int
     */
    private function updateRecordStatusMany(array $ids, $recordStatus, $method)
    {
        if (count($ids)) {
            $sets[ 'record_status' ] = $recordStatus;
            $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';

            $this->qb->whereIn($primaryKey, $ids);

            if (method_exists($this, 'updateRecordSets')) {
                $this->updateRecordSets($sets[ $key ]);
            }

            if (method_exists($this, $beforeMethod = 'before' . ucfirst($method))) {
                call_user_func_array([&$this, $beforeMethod], [$sets]);
            }

            if ($this->qb->table($this->table)->update($sets)) {
                if (method_exists($this, $afterMethod = 'after' . ucfirst($method))) {
                    return call_user_func([&$this, $beforeMethod]);
                }

                return $this->getAffectedRows();
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::updateRecordStatusBy
     *
     * @param int   $id
     * @param array $conditions
     *
     * @return bool|int
     */
    private function updateRecordStatusBy(array $conditions = [], $recordStatus, $method)
    {
        if (count($conditions)) {
            $sets[ 'record_status' ] = $recordStatus;

            if (method_exists($this, 'updateRecordSets')) {
                $this->updateRecordSets($sets);
            }

            if (method_exists($this, $beforeMethod = 'before' . ucfirst($method))) {
                call_user_func_array([&$this, $beforeMethod], [$sets]);
            }

            if ($this->qb->table($this->table)->update($sets, $conditions)) {
                if (method_exists($this, $afterMethod = 'after' . ucfirst($method))) {
                    return call_user_func([&$this, $beforeMethod]);
                }

                return $this->getAffectedRows();
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::updateRecordStatusManyBy
     *
     * @param array $ids
     * @param array $conditions
     *
     * @return bool|int
     */
    private function updateRecordStatusManyBy($conditions = [], $recordStatus, $method)
    {
        if (count($conditions)) {
            $sets[ 'record_status' ] = $recordStatus;

            if (method_exists($this, 'updateRecordSets')) {
                $this->updateRecordSets($sets);
            }

            if (method_exists($this, $beforeMethod = 'before' . ucfirst($method))) {
                call_user_func_array([&$this, $beforeMethod], [$sets]);
            }

            if ($this->qb->table($this->table)->update($sets, $conditions)) {
                if (method_exists($this, $afterMethod = 'after' . ucfirst($method))) {
                    return call_user_func([&$this, $beforeMethod]);
                }

                return $this->getAffectedRows();
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::publish
     *
     * @param int $id
     *
     * @return bool
     */
    public function publish($id)
    {
        return $this->updateRecordStatus($id, 'PUBLISH', 'publish');
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::publishBy
     *
     * @param array $conditions
     *
     * @return bool|int
     */
    public function publishBy(array $conditions = [])
    {
        return $this->updateRecordStatusBy($conditions, 'PUBLISH', 'publishBy');
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::publishMany
     *
     * @param array $ids
     *
     * @return bool|int
     */
    public function publishMany(array $ids)
    {
        return $this->updateRecordStatusMany($ids, 'PUBLISH', 'publishMany');
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::publishManyBy
     *
     * @param array $conditions
     *
     * @return bool|int
     */
    public function publishManyBy($conditions = [])
    {
        return $this->updateRecordStatusManyBy($ids, 'PUBLISH', 'publishManyBy');
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTriat::restore
     *
     * @param int $id
     *
     * @return bool
     */
    public function restore($id)
    {
        return $this->updateRecordStatus($id, 'PUBLISH', 'restore');
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::restoreBy
     *
     * @param array $conditions
     *
     * @return bool
     */
    public function restoreBy(array $conditions = [])
    {
        return $this->updateRecordStatusBy($conditions, 'PUBLISH', 'restoreBy');
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::restoreMany
     *
     * @param array $ids
     *
     * @return bool|int
     */
    public function restoreMany(array $ids)
    {
        return $this->updateRecordStatusMany($ids, 'PUBLISH', 'restoreMany');
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::restoreManyBy
     *
     * @param array $conditions
     *
     * @return bool|int
     */
    public function restoreManyBy(array $ids, $conditions = [])
    {
        return $this->updateRecordStatusManyBy($ids, 'PUBLISH', 'restoreManyBy');
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTriat::unpublish
     *
     * @param int $id
     *
     * @return bool
     */
    public function unpublish($id)
    {
        return $this->updateRecordStatus($id, 'UNPUBLISH', 'unpublish');
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::unpublishBy
     *
     * @param array $conditions
     *
     * @return bool
     */
    public function unpublishBy(array $conditions = [])
    {
        return $this->updateRecordStatusBy($conditions, 'UNPUBLISH', 'unpublishBy');
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::unpublishMany
     *
     * @param array $ids
     *
     * @return bool|int
     */
    public function unpublishMany(array $ids)
    {
        return $this->updateRecordStatusMany($ids, 'UNPUBLISH', 'unpublishMany');
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::unpublishManyBy
     *
     * @param array $conditions
     *
     * @return bool|int
     */
    public function unpublishManyBy(array $ids, $conditions = [])
    {
        return $this->updateRecordStatusManyBy($ids, 'UNPUBLISH', 'unpublishManyBy');
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTriat::softDelete
     *
     * @param int $id
     *
     * @return bool
     */
    public function softDelete($id)
    {
        return $this->updateRecordStatus($id, 'DELETED', 'softDelete');
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::softDeleteBy
     *
     * @param array $conditions
     *
     * @return bool
     */
    public function softDeleteBy(array $conditions = [])
    {
        return $this->updateRecordStatusBy($conditions, 'DELETED', 'softDeleteBy');
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::softDeleteMany
     *
     * @param array $ids
     *
     * @return bool|int
     */
    public function softDeleteMany(array $ids)
    {
        return $this->updateRecordStatusMany($ids, 'DELETED', 'softDeleteMany');
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::softDeleteManyBy
     *
     * @param array $conditions
     *
     * @return bool|int
     */
    public function softDeleteManyBy(array $ids, $conditions = [])
    {
        return $this->updateRecordStatusManyBy($ids, 'DELETED', 'softDeleteManyBy');
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTriat::archive
     *
     * @param int $id
     *
     * @return bool
     */
    public function archive($id)
    {
        return $this->updateRecordStatus($id, 'ARCHIVED', 'archive');
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::archiveBy
     *
     * @param array $conditions
     *
     * @return bool
     */
    public function archiveBy(array $conditions = [])
    {
        return $this->updateRecordStatusBy($conditions, 'ARCHIVED', 'archiveBy');
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::archiveMany
     *
     * @param array $ids
     *
     * @return bool|int
     */
    public function archiveMany(array $ids)
    {
        return $this->updateRecordStatusMany($ids, 'ARCHIVED', 'archiveMany');
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::archiveManyBy
     *
     * @param array $conditions
     *
     * @return bool|int
     */
    public function archiveManyBy(array $ids, $conditions = [])
    {
        return $this->updateRecordStatusManyBy($ids, 'ARCHIVED', 'archiveManyBy');
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTriat::lock
     *
     * @param int $id
     *
     * @return bool
     */
    public function lock($id)
    {
        return $this->updateRecordStatus($id, 'LOCKED', 'lock');
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::lockBy
     *
     * @param array $conditions
     *
     * @return bool
     */
    public function lockBy(array $conditions = [])
    {
        return $this->updateRecordStatusBy($conditions, 'LOCKED', 'lockBy');
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::lockMany
     *
     * @param array $ids
     *
     * @return bool|int
     */
    public function lockMany(array $ids)
    {
        return $this->updateRecordStatusMany($ids, 'LOCKED', 'lockMany');
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::lockManyBy
     *
     * @param array $conditions
     *
     * @return bool|int
     */
    public function lockManyBy(array $ids, $conditions = [])
    {
        return $this->updateRecordStatusManyBy($ids, 'LOCKED', 'lockManyBy');
    }
}