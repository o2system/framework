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
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Framework\Models\Sql\DataObjects\Result;
use O2System\Framework\Models\Sql\DataObjects\Result\Row;
use O2System\Image\Uploader;

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
            }

            if (method_exists($this, 'getRecordOrdering')) {
                if ($this->recordOrdering === true && empty($sets[ 'record_ordering' ])) {
                    $sets[ 'record_ordering' ] = $this->getRecordOrdering();
                }
            }

            if (isset($this->uploadedImageFilePath)) {
                if ( ! file_exists($this->uploadedImageFilePath)) {
                    mkdir($this->uploadedImageFilePath, 0777, true);
                }

                $upload = new Uploader();
                $upload->setPath($this->uploadedImageFilePath);

                if ($files = input()->files()) {
                    // Uploaded Image Process
                    if (isset($this->uploadedImageKey)) {
                        if (isset($files[ $this->uploadedImageKey ])) {
                            $upload->process($this->uploadedImageKey);

                            if ($upload->getErrors()) {
                                $errors = new Unordered();

                                foreach ($upload->getErrors() as $code => $error) {
                                    $errors->createList($error);
                                }

                                if (services()->has('session')) {
                                    session()->setFlash('danger', $errors);
                                }

                                return false;
                            }
                        }
                    } elseif (count($this->uploadedImageKeys)) {
                        foreach ($this->uploadedImageKeys as $uploadedImageKey) {
                            if (isset($files[ $uploadedImageKey ])) {
                                $upload->process($uploadedImageKey);

                                if ($upload->getErrors()) {
                                    $errors = new Unordered();

                                    foreach ($upload->getErrors() as $code => $error) {
                                        $errors->createList($error);
                                    }

                                    if (services()->has('session')) {
                                        session()->setFlash('danger', $errors);
                                    }

                                    return false;
                                }
                            }
                        }
                    }

                    // Uploaded File Process
                    if (isset($this->uploadedFileFilepath)) {
                        if (isset($files[ $this->uploadedFileKey ])) {
                            $upload->process($this->uploadedFileKey);

                            if ($upload->getErrors()) {
                                $errors = new Unordered();

                                foreach ($upload->getErrors() as $code => $error) {
                                    $errors->createList($error);
                                }

                                if (services()->has('session')) {
                                    session()->setFlash('danger', $errors);
                                }

                                return false;
                            }
                        }
                    } elseif (count($this->uploadedFileKeys)) {
                        foreach ($this->uploadedFileKeys as $uploadedFileKey) {
                            if (isset($files[ $uploadedFileKey ])) {
                                $upload->process($uploadedFileKey);

                                if ($upload->getErrors()) {
                                    $errors = new Unordered();

                                    foreach ($upload->getErrors() as $code => $error) {
                                        $errors->createList($error);
                                    }

                                    if (services()->has('session')) {
                                        session()->setFlash('danger', $errors);
                                    }

                                    return false;
                                }
                            }
                        }
                    }
                }
            }

            if ($this->qb->table($this->table)->insert($sets)) {
                if (method_exists($this, 'afterInsert')) {
                    $this->afterInsert();
                }

                if (method_exists($this, 'rebuildTree')) {
                    $this->rebuildTree();
                }

                $label = false;
                foreach (['name', 'label', 'title', 'code'] as $labelField) {
                    if (isset($sets[ $labelField ])) {
                        if(services()->has('session')) {
                            session()->setFlash('success', language('SUCCESS_INSERT_WITH_LABEL', [$sets[ $labelField ]]));
                        }

                        $label = true;
                        break;
                    }
                }

                if ($label === false) {
                    if(services()->has('session')) {
                        session()->setFlash('success', language('SUCCESS_INSERT'));
                    }
                }

                return true;
            }
        }

        $label = false;
        foreach (['name', 'label', 'title', 'code'] as $labelField) {
            if (isset($sets[ $labelField ])) {
                if(services()->has('session')) {
                    session()->setFlash('danger', language('FAILED_INSERT_WITH_LABEL', [$sets[ $labelField ]]));
                }

                $label = true;
                break;
            }
        }

        if ($label === false) {
            if(services()->has('session')) {
                session()->setFlash('danger', language('FAILED_INSERT'));
            }
        }

        // Sets Global $_POST Variable
        $_POST = $sets;

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
            }

            if ($this->qb->table($this->table)->insertBatch($sets)) {
                if (method_exists($this, 'afterInsertMany')) {
                    $this->afterInsertMany();
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
            }

            if (method_exists($this, 'getRecordOrdering')) {
                if ($this->recordOrdering === true && empty($sets[ 'record_ordering' ])) {
                    $sets[ 'record_ordering' ] = $this->getRecordOrdering();
                }
            }

            if ($row = $this->findWhere($conditions)) {
                if (isset($this->uploadedImageFilePath)) {
                    if ( ! file_exists($this->uploadedImageFilePath)) {
                        mkdir($this->uploadedImageFilePath, 0777, true);
                    }

                    $upload = new Uploader();
                    $upload->setPath($this->uploadedImageFilePath);

                    if ($files = input()->files()) {
                        // Uploaded Image Process
                        if (isset($this->uploadedImageKey)) {
                            if (isset($files[ $this->uploadedImageKey ])) {
                                $upload->process($this->uploadedImageKey);

                                if ($upload->getErrors()) {
                                    $errors = new Unordered();

                                    foreach ($upload->getErrors() as $code => $error) {
                                        $errors->createList($error);
                                    }

                                    if (services()->has('session')) {
                                        session()->setFlash('danger', $errors);
                                    }

                                    return false;
                                } elseif ($row->offsetGet($this->uploadedImageKey) !== $upload->getUploadedFiles()->first()[ 'name' ]) {
                                    $sets[ $this->uploadedImageKey ] = $upload->getUploadedFiles()->first()[ 'name' ];

                                    if (is_file($filePath = $this->uploadedImageFilePath . $row->offsetGet($this->uploadedImageKey))) {
                                        unlink($filePath);
                                    }
                                }
                            }
                        } elseif (count($this->uploadedImageKeys)) {
                            foreach ($this->uploadedImageKeys as $uploadedImageKey) {
                                if (isset($files[ $uploadedImageKey ])) {
                                    $upload->process($uploadedImageKey);

                                    if ($upload->getErrors()) {
                                        $errors = new Unordered();

                                        foreach ($upload->getErrors() as $code => $error) {
                                            $errors->createList($error);
                                        }

                                        if (services()->has('session')) {
                                            session()->setFlash('danger', $errors);
                                        }

                                        return false;
                                    } elseif ($row->offsetGet($uploadedImageKey) !== $upload->getUploadedFiles()->first()[ 'name' ]) {
                                        $sets[ $uploadedImageKey ] = $upload->getUploadedFiles()->first()[ 'name' ];

                                        if (is_file($filePath = $this->uploadedImageFilePath . $row->offsetGet($uploadedImageKey))) {
                                            unlink($filePath);
                                        }
                                    }
                                }
                            }
                        }

                        // Uploaded File Process
                        if (isset($this->uploadedFileFilepath)) {
                            if (isset($files[ $this->uploadedFileKey ])) {
                                $upload->process($this->uploadedFileKey);

                                if ($upload->getErrors()) {
                                    $errors = new Unordered();

                                    foreach ($upload->getErrors() as $code => $error) {
                                        $errors->createList($error);
                                    }

                                    if (services()->has('session')) {
                                        session()->setFlash('danger', $errors);
                                    }

                                    return false;
                                } elseif ($row->offsetGet($this->uploadedFileKey) !== $upload->getUploadedFiles()->first()[ 'name' ]) {
                                    $sets[ $this->uploadedFileKey ] = $upload->getUploadedFiles()->first()[ 'name' ];

                                    if (is_file($filePath = $this->uploadedFileFilepath . $row->offsetGet($this->uploadedFileKey))) {
                                        unlink($filePath);
                                    }
                                }
                            }
                        } elseif (count($this->uploadedFileKeys)) {
                            foreach ($this->uploadedFileKeys as $uploadedFileKey) {
                                if (isset($files[ $uploadedFileKey ])) {
                                    $upload->process($uploadedFileKey);

                                    if ($upload->getErrors()) {
                                        $errors = new Unordered();

                                        foreach ($upload->getErrors() as $code => $error) {
                                            $errors->createList($error);
                                        }

                                        if (services()->has('session')) {
                                            session()->setFlash('danger', $errors);
                                        }

                                        return false;
                                    } elseif ($row->offsetGet($uploadedFileKey) !== $upload->getUploadedFiles()->first()[ 'name' ]) {
                                        $sets[ $uploadedFileKey ] = $upload->getUploadedFiles()->first()[ 'name' ];

                                        if (is_file($filePath = $this->uploadedFileFilepath . $row->offsetGet($uploadedFileKey))) {
                                            unlink($filePath);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if ($this->qb->table($this->table)->update($sets, $conditions)) {

                    if (method_exists($this, 'afterUpdate')) {
                        $this->afterUpdate();
                    }

                    $label = false;
                    foreach (['name', 'label', 'title', 'code'] as $labelField) {
                        if (isset($sets[ $labelField ])) {
                            if(services()->has('session')) {
                                session()->setFlash('success',
                                    language('SUCCESS_UPDATE_WITH_LABEL', [$sets[ $labelField ]]));
                            }

                            $label = true;
                            break;
                        }
                    }

                    if ($label === false) {
                        if(services()->has('session')) {
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
                if(services()->has('session')) {
                    session()->setFlash('danger', language('FAILED_UPDATE_WITH_LABEL', [$sets[ $labelField ]]));
                }

                $label = true;
                break;
            }
        }

        if ($label === false) {
            if(services()->has('session')) {
                session()->setFlash('danger', language('FAILED_UPDATE'));
            }
        }

        // Sets Global $_POST Variable
        $_POST = $sets;

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
        }

        if ($this->qb->table($this->table)->updateBatch($sets, $primaryKey)) {
            if (method_exists($this, 'afterUpdateMany')) {
                return $this->afterUpdateMany();
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
        if (method_exists($this, 'beforeDelete')) {
            $this->beforeDelete();
        }

        if ($id instanceof Row) {
            $row = $id;
        } else {
            $row = $this->find($id);
        }

        // Delete Files
        if ($row instanceof Row) {
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

            if ( ! $id instanceof Row) {
                if ($row->delete()) {
                    if (method_exists($this, 'afterDelete')) {
                        $this->afterDelete();
                    }

                    if (method_exists($this, 'rebuildTree')) {
                        $this->rebuildTree();
                    }
                }
            }

            return $row->delete();
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
                $this->beforeDelete();
            }

            $affectedRows = 0;
            if ($result = $this->findWhere($conditions)) {
                if ($result instanceof Result) {
                    foreach ($result as $row) {
                        if ($this->delete($row)) {
                            $affectedRows++;
                        }
                    }
                } elseif ($result instanceof Row) {
                    if ($this->delete($result)) {
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
}