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

use O2System\Image\Uploader;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Framework\Models\Sql\DataObjects\Result;
use O2System\Framework\Models\Sql\DataObjects\Result\Row;

/**
 * Class TraitModifier
 *
 * @package O2System\Framework\Models\Sql\Traits
 */
trait ModifierTrait
{
    /**
     * ModifierTrait::$enabledFlashMessage
     *
     * @var bool
     */
    protected $flashMessage = false;

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::flashMessage
     *
     * @return static
     */
    public function flashMessage($enabled)
    {
        $this->flashMessage = (bool)$enabled;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::insert
     *
     * @param array $sets
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     */
    public function insert(array $sets)
    {
        if (count($sets)) {
            if (count($this->fillableColumns)) {
                foreach ($sets as $key => $value) {
                    if ( ! in_array($key, $this->fillableColumns)) {
                        unset($sets[ $key ]);
                    }
                }
            }

            if (method_exists($this, 'insertRecordSets')) {
                $this->insertRecordSets($sets);
            }

            if (method_exists($this, 'beforeInsert')) {
                $this->beforeInsert($sets);
            } elseif (method_exists($this, 'beforeInsertOrUpdate')) {
                $this->beforeInsertOrUpdate($sets);
            }

            if (method_exists($this, 'getRecordOrdering')) {
                if ($this->recordOrdering === true && empty($sets[ 'record_ordering' ])) {
                    $sets[ 'record_ordering' ] = $this->getRecordOrdering();
                }
            }

            if (isset($this->uploadedImageFilePath)) {
                if ($files = input()->files()) {
                    $files->setPath($this->uploadedFileFilepath);

                    if ($files->process() === false) {
                        foreach ($files->getErrors() as $code => $error) {
                            $errors->createList($error);
                        }

                        if (services()->has('session') and $this->flashMessage) {
                            session()->setFlash('danger', $errors);
                        }

                        return false;
                    }

                    // Stored Images Sets
                    if (isset($this->uploadedImageKey)) {
                        $sets[ $this->uploadedImageKey ] = $files->offsetGet($this->uploadedImageKey);

                        if ($storedFiles = $files->offsetGet($this->uploadedImageKey)) {
                            if (is_array($storedFiles)) {
                                foreach ($storedFiles as $storedFile) {
                                    $sets[ $this->uploadedImageKey ][] = $storedFile->getClientFilename();
                                }
                            } else {
                                $sets[ $this->uploadedImageKey ] = $storedFile->getClientFilename();
                            }
                        }
                    } elseif (isset($this->uploadedImageKeys)) {
                        foreach ($this->uploadedImageKeys as $uploadedImageKey) {
                            $sets[ $uploadedImageKey ] = $files->offsetGet($uploadedImageKey);

                            if ($storedFiles = $files->offsetGet($uploadedImageKey)) {
                                if (is_array($storedFiles)) {
                                    foreach ($storedFiles as $storedFile) {
                                        $sets[ $uploadedImageKey ][] = $storedFile->getClientFilename();
                                    }
                                } else {
                                    $sets[ $uploadedImageKey ] = $storedFile->getClientFilename();
                                }
                            }
                        }
                    }

                    // Stored Files Sets
                    if (isset($this->uploadedFileKey)) {
                        $sets[ $this->uploadedFileKey ] = $files->offsetGet($this->uploadedFileKey);

                        if ($storedFiles = $files->offsetGet($this->uploadedFileKey)) {
                            if (is_array($storedFiles)) {
                                foreach ($storedFiles as $storedFile) {
                                    $sets[ $this->uploadedFileKey ][] = $storedFile->getClientFilename();
                                }
                            } else {
                                $sets[ $this->uploadedFileKey ] = $storedFile->getClientFilename();
                            }
                        }
                    } elseif (isset($this->uploadedFileKeys)) {
                        foreach ($this->uploadedFileKeys as $uploadedFileKey) {
                            $sets[ $uploadedFileKey ] = $files->offsetGet($uploadedFileKey);

                            if ($storedFiles = $files->offsetGet($uploadedFileKey)) {
                                if (is_array($storedFiles)) {
                                    foreach ($storedFiles as $storedFile) {
                                        $sets[ $uploadedFileKey ][] = $storedFile->getClientFilename();
                                    }
                                } else {
                                    $sets[ $uploadedFileKey ] = $storedFile->getClientFilename();
                                }
                            }
                        }
                    }
                }
            }

            if ($this->qb->table($this->table)->insert($sets)) {
                if (method_exists($this, 'afterInsert')) {
                    $this->afterInsert($sets);
                } elseif (method_exists($this, 'afterInsertOrUpdate')) {
                    $this->afterInsertOrUpdate($sets);
                }

                if (method_exists($this, 'rebuildTree')) {
                    $this->rebuildTree();
                }

                $label = false;
                foreach (['name', 'label', 'title', 'code'] as $labelField) {
                    if (isset($sets[ $labelField ])) {
                        if (services()->has('session') and $this->flashMessage) {
                            session()->setFlash('success',
                                language('SUCCESS_INSERT_WITH_LABEL', [$sets[ $labelField ]]));
                        }

                        $label = true;
                        break;
                    }
                }

                if ($label === false) {
                    if (services()->has('session') and $this->flashMessage) {
                        session()->setFlash('success', language('SUCCESS_INSERT'));
                    }
                }

                return true;
            }
        }

        $label = false;
        foreach (['name', 'label', 'title', 'code'] as $labelField) {
            if (isset($sets[ $labelField ])) {
                if (services()->has('session') and $this->flashMessage) {
                    session()->setFlash('danger', language('FAILED_INSERT_WITH_LABEL', [$sets[ $labelField ]]));
                }

                $label = true;
                break;
            }
        }

        if ($label === false) {
            if (services()->has('session') and $this->flashMessage) {
                session()->setFlash('danger', language('FAILED_INSERT'));
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::insertOrUpdate
     *
     * @param array $sets
     * @param array $conditions
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     */
    public function insertOrUpdate(array $sets, array $conditions = [])
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
                if ($result->count() > 0) {
                    return $this->update($sets, $conditions);
                } else {
                    return $this->insert($sets);
                }
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
                        $set[ 'record_ordering' ] = $this->getRecordOrdering();
                    }
                }
            }

            if (method_exists($this, 'beforeInsertMany')) {
                $this->beforeInsertMany($sets);
            } elseif (method_exists($this, 'beforeInsertOrUpdateMany')) {
                $this->beforeInsertOrUpdateMany($sets);
            }

            if ($this->qb->table($this->table)->insertBatch($sets)) {
                if (method_exists($this, 'afterInsertMany')) {
                    $this->afterInsertMany($sets);
                } elseif (method_exists($this, 'afterInsertOrUpdateMany')) {
                    $this->afterInsertOrUpdateMany($sets);
                }

                $affectedRows = $this->db->getAffectedRows();

                if (method_exists($this, 'rebuildTree')) {
                    $this->rebuildTree();
                }

                return $affectedRows;
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
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     */
    public function insertIfNotExists(array $sets, array $conditions = [])
    {
        if (empty($conditions)) {
            $conditions = $sets;
        }

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
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     */
    public function update(array $sets, array $conditions = [])
    {
        if (count($sets)) {
            $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';

            if (empty($conditions)) {
                if (isset($sets[ $primaryKey ])) {
                    $conditions = [$primaryKey => $sets[ $primaryKey ]];
                }
            }

            if (count($this->fillableColumns)) {
                foreach ($sets as $key => $value) {
                    if ( ! in_array($key, $this->fillableColumns)) {
                        unset($sets[ $key ]);
                    }
                }
            }

            if (method_exists($this, 'updateRecordSets')) {
                $this->updateRecordSets($sets);
            }

            if (method_exists($this, 'beforeUpdate')) {
                $sets = $this->beforeUpdate($sets);
            } elseif (method_exists($this, 'beforeInsertOrUpdate')) {
                $sets = $this->beforeInsertOrUpdate($sets);
            }

            if (method_exists($this, 'getRecordOrdering')) {
                if ($this->recordOrdering === true && empty($sets[ 'record_ordering' ])) {
                    $sets[ 'record_ordering' ] = $this->getRecordOrdering();
                }
            }

            if ($row = $this->findWhere($conditions)) {
                if (isset($this->uploadedImageFilePath)) {
                    if ($files = input()->files()) {
                        $files->setPath($this->uploadedFileFilepath);

                        if ($files->process() === false) {
                            foreach ($files->getErrors() as $code => $error) {
                                $errors->createList($error);
                            }

                            if (services()->has('session') and $this->flashMessage) {
                                session()->setFlash('danger', $errors);
                            }

                            return false;
                        }

                        // Stored Images Sets
                        if (isset($this->uploadedImageKey)) {
                            $sets[ $this->uploadedImageKey ] = $files->offsetGet($this->uploadedImageKey);

                            if ($storedFiles = $files->offsetGet($this->uploadedImageKey)) {
                                if (is_array($storedFiles)) {
                                    foreach ($storedFiles as $storedFile) {
                                        $sets[ $this->uploadedImageKey ][] = $storedFile->getClientFilename();
                                    }
                                } else {
                                    $sets[ $this->uploadedImageKey ] = $storedFile->getClientFilename();
                                }
                            }
                        } elseif (isset($this->uploadedImageKeys)) {
                            foreach ($this->uploadedImageKeys as $uploadedImageKey) {
                                $sets[ $uploadedImageKey ] = $files->offsetGet($uploadedImageKey);

                                if ($storedFiles = $files->offsetGet($uploadedImageKey)) {
                                    if (is_array($storedFiles)) {
                                        foreach ($storedFiles as $storedFile) {
                                            $sets[ $uploadedImageKey ][] = $storedFile->getClientFilename();
                                        }
                                    } else {
                                        $sets[ $uploadedImageKey ] = $storedFile->getClientFilename();
                                    }
                                }
                            }
                        }

                        // Stored Files Sets
                        if (isset($this->uploadedFileKey)) {
                            $sets[ $this->uploadedFileKey ] = $files->offsetGet($this->uploadedFileKey);

                            if ($storedFiles = $files->offsetGet($this->uploadedFileKey)) {
                                if (is_array($storedFiles)) {
                                    foreach ($storedFiles as $storedFile) {
                                        $sets[ $this->uploadedFileKey ][] = $storedFile->getClientFilename();
                                    }
                                } else {
                                    $sets[ $this->uploadedFileKey ] = $storedFile->getClientFilename();
                                }
                            }
                        } elseif (isset($this->uploadedFileKeys)) {
                            foreach ($this->uploadedFileKeys as $uploadedFileKey) {
                                $sets[ $uploadedFileKey ] = $files->offsetGet($uploadedFileKey);

                                if ($storedFiles = $files->offsetGet($uploadedFileKey)) {
                                    if (is_array($storedFiles)) {
                                        foreach ($storedFiles as $storedFile) {
                                            $sets[ $uploadedFileKey ][] = $storedFile->getClientFilename();
                                        }
                                    } else {
                                        $sets[ $uploadedFileKey ] = $storedFile->getClientFilename();
                                    }
                                }
                            }
                        }
                    }
                }

                if ($this->qb->table($this->table)->update($sets, $conditions)) {

                    if (method_exists($this, 'afterUpdate')) {
                        $this->afterUpdate($sets);
                    } elseif (method_exists($this, 'afterInsertOrUpdate')) {
                        $this->afterInsertOrUpdate($sets);
                    }

                    $label = false;
                    foreach (['name', 'label', 'title', 'code'] as $labelField) {
                        if (isset($sets[ $labelField ])) {
                            if (services()->has('session') and $this->flashMessage) {
                                session()->setFlash('success',
                                    language('SUCCESS_UPDATE_WITH_LABEL', [$sets[ $labelField ]]));
                            }

                            $label = true;
                            break;
                        }
                    }

                    if ($label === false) {
                        if (services()->has('session') and $this->flashMessage) {
                            session()->setFlash('success', language('SUCCESS_UPDATE'));
                        }
                    }

                    return true;
                }
            }
        }

        $label = false;
        foreach (['name', 'label', 'title', 'code'] as $labelField) {
            if (isset($sets[ $labelField ])) {
                if (services()->has('session') and $this->flashMessage) {
                    session()->setFlash('danger', language('FAILED_UPDATE_WITH_LABEL', [$sets[ $labelField ]]));
                }

                $label = true;
                break;
            }
        }

        if ($label === false) {
            if (services()->has('session') and $this->flashMessage) {
                session()->setFlash('danger', language('FAILED_UPDATE'));
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
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     */
    public function updateOrInsert(array $sets, array $conditions = [])
    {
        return $this->insertOrUpdate($sets, $conditions);
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::updateMany
     *
     * @param array $sets
     *
     * @return bool|array
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
        } elseif (method_exists($this, 'beforeInsertOrUpdateMany')) {
            $this->beforeInsertOrUpdateMany($sets);
        }

        if ($this->qb->table($this->table)->updateBatch($sets, $primaryKey)) {
            if (method_exists($this, 'afterUpdateMany')) {
                return $this->afterUpdateMany($sets);
            } elseif (method_exists($this, 'afterInsertOrUpdateMany')) {
                return $this->afterInsertOrUpdateMany($sets);
            }

            $affectedRows = $this->db->getAffectedRows();

            if (method_exists($this, 'rebuildTree')) {
                $this->rebuildTree();
            }

            return $affectedRows;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::delete
     *
     * @param int|Row $id
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function delete($id)
    {
        $params = func_get_args();

        if (method_exists($this, 'beforeDelete')) {
            call_user_func_array([&$this, 'beforeDelete'], $params);
        }

        if (empty($this->primaryKeys)) {
            $conditions = [$this->primaryKey => reset($params)];
        } else {
            foreach ($this->primaryKeys as $key => $primaryKey) {
                $conditions[ $primaryKey ] = $params[ $key ];
            }
        }

        $affectedRows = 0;
        if ($result = $this->findWhere($conditions)) {
            if($result instanceof Result) {
                foreach($result as $row) {
                    $this->deleteRow($row);
                    $affectedRows++;
                }
            } elseif($result instanceof Row) {
                $this->deleteRow($result);
                $affectedRows++;
            }
        }

        if ($affectedRows > 0) {
            if (method_exists($this, 'rebuildTree')) {
                $this->rebuildTree();
            }

            if (method_exists($this, 'afterDelete')) {
                call_user_func_array([&$this, 'afterDelete'], $params);
            }

            return $affectedRows;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::deleteRow
     *
     * @param \O2System\Framework\Models\Sql\DataObjects\Result\Row $row
     *
     * @return bool|mixed
     */
    private function deleteRow(Row $row)
    {
        if (isset($this->uploadedImageFilePath)) {

            // Remove Uploaded Image
            if ($this->uploadedImageKey) {
                if (is_file($filePath = $this->uploadedImageFilePath . $row->offsetGet($this->uploadedImageKey))) {
                    unlink($filePath);
                }
            } elseif (count($this->uploadedImageKeys)) {
                foreach ($this->uploadedImageKeys as $uploadedImageKey) {
                    if (is_file($filePath = $this->uploadedImageFilePath . $row->offsetGet($uploadedImageKey))) {
                        unlink($filePath);
                    }
                }
            }

            // Remove Uploaded File
            if ($this->uploadedFileFilepath) {
                if (is_file($filePath = $this->uploadedFileFilepath . $row->offsetGet($this->uploadedFileKey))) {
                    unlink($filePath);
                }
            } elseif (count($this->uploadedFileKeys)) {
                foreach ($this->uploadedFileKeys as $uploadedFileKey) {
                    if (is_file($filePath = $this->uploadedFileFilepath . $row->offsetGet($uploadedFileKey))) {
                        unlink($filePath);
                    }
                }
            }
        }

        if (empty($this->primaryKeys)) {
            $conditions = [$this->primaryKey => $row->offsetGet($this->primaryKey)];
        } else {
            foreach ($this->primaryKeys as $key => $primaryKey) {
                $conditions[ $primaryKey ] = $row->offsetGet($primaryKey);
            }
        }

        if ($this->qb->table($this->table)->limit(1)->delete($conditions)) {
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
     * @return bool|int
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function deleteBy($conditions = [])
    {
        if (count($conditions)) {
            if (method_exists($this, 'beforeDelete')) {
                $this->beforeDelete($conditions);
            }

            $affectedRows = 0;
            if ($result = $this->findWhere($conditions)) {
                if($result instanceof Result) {
                    foreach($result as $row) {
                        $this->deleteRow($row);
                        $affectedRows++;
                    }
                } elseif($result instanceof Row) {
                    $this->deleteRow($result);
                    $affectedRows++;
                }
            }

            if ($affectedRows > 0) {
                if (method_exists($this, 'rebuildTree')) {
                    $this->rebuildTree();
                }

                if (method_exists($this, 'afterDelete')) {
                    $this->afterDelete($conditions);
                }

                return $affectedRows;
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
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function deleteMany(array $ids)
    {
        if (method_exists($this, 'beforeDelete')) {
            $this->beforeDelete();
        }

        $affectedRows = 0;
        if ($result = $this->findIn($ids)) {
            foreach ($result as $row) {
                if ($this->delete($row)) {
                    $affectedRows++;
                }
            }
        }

        if ($affectedRows > 0) {
            if (method_exists($this, 'afterDelete')) {
                $this->afterDelete();
            }

            if (method_exists($this, 'rebuildTree')) {
                $this->rebuildTree();
            }

            return $affectedRows;
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
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function deleteManyBy($conditions = [])
    {
        return $this->deleteBy($conditions);
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::updateRecordStatus
     *
     * @param int    $id
     * @param string $recordStatus
     * @param string $method
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
                call_user_func([&$this, $beforeMethod]);
            }

            if (method_exists($this, 'rebuildTree')) {
                $this->rebuildTree();
            }

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::updateRecordStatusMany
     *
     * @param array  $ids
     * @param string $recordStatus
     * @param string $method
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
                $this->updateRecordSets($sets);
            }

            if (method_exists($this, $beforeMethod = 'before' . ucfirst($method))) {
                call_user_func_array([&$this, $beforeMethod], [$sets]);
            }

            if ($this->qb->table($this->table)->update($sets)) {
                if (method_exists($this, $afterMethod = 'after' . ucfirst($method))) {
                    call_user_func([&$this, $beforeMethod]);
                }

                $affectedRows = $this->db->getAffectedRows();

                if (method_exists($this, 'rebuildTree')) {
                    $this->rebuildTree();
                }

                return $affectedRows;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::updateRecordStatusBy
     *
     * @param string $recordStatus
     * @param string $method
     * @param array  $conditions
     *
     * @return bool|int
     */
    private function updateRecordStatusBy($recordStatus, $method, array $conditions)
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
                    call_user_func([&$this, $beforeMethod]);
                }

                $affectedRows = $this->db->getAffectedRows();

                if (method_exists($this, 'rebuildTree')) {
                    $this->rebuildTree();
                }

                return $affectedRows;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::updateRecordStatusManyBy
     *
     * @param string $recordStatus
     * @param string $method
     * @param array  $conditions
     *
     * @return bool|int
     */
    private function updateRecordStatusManyBy($recordStatus, $method, array $conditions)
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
                    call_user_func([&$this, $beforeMethod]);
                }

                $affectedRows = $this->db->getAffectedRows();

                if (method_exists($this, 'rebuildTree')) {
                    $this->rebuildTree();
                }

                return $affectedRows;
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
    public function publishBy(array $conditions)
    {
        return $this->updateRecordStatusBy('PUBLISH', 'publishBy', $conditions);
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
    public function publishManyBy(array $conditions)
    {
        return $this->updateRecordStatusManyBy('PUBLISH', 'publishManyBy', $conditions);
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::restore
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
    public function restoreBy(array $conditions)
    {
        return $this->updateRecordStatusBy('PUBLISH', 'restoreBy', $conditions);
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
    public function restoreManyBy(array $conditions)
    {
        return $this->updateRecordStatusManyBy('PUBLISH', 'restoreManyBy', $conditions);
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::unpublish
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
    public function unpublishBy(array $conditions)
    {
        return $this->updateRecordStatusBy('UNPUBLISH', 'unpublishBy', $conditions);
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
    public function unpublishManyBy(array $conditions)
    {
        return $this->updateRecordStatusManyBy('UNPUBLISH', 'unpublishManyBy', $conditions);
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::softDelete
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
    public function softDeleteBy(array $conditions)
    {
        return $this->updateRecordStatusBy('DELETED', 'softDeleteBy', $conditions);
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
    public function softDeleteManyBy(array $conditions)
    {
        return $this->updateRecordStatusManyBy('DELETED', 'softDeleteManyBy', $conditions);
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::archive
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
    public function archiveBy(array $conditions)
    {
        return $this->updateRecordStatusBy('ARCHIVED', 'archiveBy', $conditions);
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
    public function archiveManyBy(array $conditions)
    {
        return $this->updateRecordStatusManyBy('ARCHIVED', 'archiveManyBy', $conditions);
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::lock
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
    public function lockBy(array $conditions)
    {
        return $this->updateRecordStatusBy('LOCKED', 'lockBy', $conditions);
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
    public function lockManyBy(array $conditions)
    {
        return $this->updateRecordStatusManyBy('LOCKED', 'lockManyBy', $conditions);
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::draft
     *
     * @param int $id
     *
     * @return bool
     */
    public function draft($id)
    {
        return $this->updateRecordStatus($id, 'DRAFT', 'draft');
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::draftBy
     *
     * @param array $conditions
     *
     * @return bool
     */
    public function draftBy(array $conditions)
    {
        return $this->updateRecordStatusBy('DRAFT', 'draftBy', $conditions);
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::draftMany
     *
     * @param array $ids
     *
     * @return bool|int
     */
    public function draftMany(array $ids)
    {
        return $this->updateRecordStatusMany($ids, 'DRAFT', 'draftMany');
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::draftManyBy
     *
     * @param array $conditions
     *
     * @return bool|int
     */
    public function draftManyBy(array $conditions)
    {
        return $this->updateRecordStatusManyBy('DRAFT', 'draftManyBy', $conditions);
    }
}