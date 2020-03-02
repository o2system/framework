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

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\System\Metadata;
use O2System\Framework\Models\Sql\System\Settings;
use O2System\Image\Uploader;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Framework\Models\Sql\DataObjects\Result;
use O2System\Framework\Models\Sql\DataObjects\Result\Row;
use O2System\Kernel\Http\Message\UploadFile;
use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class TraitModifier
 *
 * @package O2System\Framework\Models\Sql\Traits
 */
trait ModifierTrait
{
    /**
     * ModifierTrait::$uploadedFiles
     *
     * @var array
     */
    protected $uploadedFiles;

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
     * ModifierTrait::getUploadedFiles
     *
     * @param array $files
     * @param mixed $uploadFilePaths
     * @return array
     */
    private function getUploadedFiles(array $files, $uploadFilePaths, array &$sets)
    {
        $files = array_intersect_key($files, $uploadFilePaths);
        foreach ($files as $field => &$file) {
            if ($file instanceof UploadFile) {
                $file->setPath($uploadFilePaths[$field]);
                if ($file->store()) {
                    $sets[$field] = $file->getClientFilename();

                    if (isset($this->row[$field])) {
                        if ($sets[$field] !== $this->row[$field]) {
                            unlink($uploadFilePaths[$field] . $this->row[$field]);
                        }
                    }
                } else {
                    if (services()->has('session') and $this->flashMessage) {
                        session()->setFlash('danger', $file->getError());
                    }

                    $this->addError(__LINE__, $file->getError());

                    return false;
                }
            } elseif (is_array($file) and is_array($uploadFilePaths[$field])) {
                $file = $this->getUploadedFiles($file, $uploadFilePaths[$field], $sets);
                foreach ($file as $fileKey => $fileObject) {
                    if ($fileObject instanceof UploadFile) {
                        if (!$fileObject->isMoved) {
                            $fileObject->setPath($uploadFilePaths[$field][$fileKey]);
                            if ($fileObject->store()) {
                                $sets[$field][$fileKey] = $fileObject->getClientFilename();

                                if (isset($this->row[$field][$fileKey])) {
                                    if ($sets[$field][$fileKey] !== $this->row[$field][$fileKey]) {
                                        unlink($uploadFilePaths[$field][$fileKey] . $this->row[$field][$fileKey]);
                                    }
                                }
                            } else {
                                if (services()->has('session') and $this->flashMessage) {
                                    session()->setFlash('danger', $file->getError());
                                }

                                $this->addError(__LINE__, $file->getError());

                                return false;
                            }
                        } else {
                            if (isset($sets[$fileKey])) {
                                unset($sets[$fileKey]);
                            }

                            $sets[$field][$fileKey] = $fileObject->getClientFilename();

                            if (isset($this->row[$field][$fileKey])) {
                                if ($sets[$field][$fileKey] !== $this->row[$field][$fileKey]) {
                                    unlink($uploadFilePaths[$field][$fileKey] . $this->row[$field][$fileKey]);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $files;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::removeUploadedFiles
     *
     * @param array $files
     * @param mixed $uploadFilePaths
     * @return array
     */
    private function removeUploadedFiles(array $row, $uploadFilePaths)
    {
        $row = array_intersect_key($row, $uploadFilePaths);
        foreach ($row as $field => &$data) {
            if (is_string($data)) {
                if (is_file($filePath = $uploadFilePaths[$field] . $data)) {
                    unlink($filePath);
                }
            } else {
                if ($data instanceof SplArrayObject) {
                    $data = $data->getArrayCopy();
                }

                if (is_array($data) and is_array($uploadFilePaths[$field])) {
                    $data = $this->removeUploadedFiles($data, $uploadFilePaths[$field]);
                }
            }
        }

        return $row;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::processImagesFiles
     *
     * @param array $sets
     */
    protected function processImagesFiles(&$sets)
    {
        if ($files = input()->files() and count($this->uploadFilePaths)) {
            $this->uploadedFiles = $this->getUploadedFiles($files->getArrayCopy(), $this->uploadFilePaths, $sets);
        }
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
                    if (!in_array($key, $this->fillableColumns)) {
                        unset($sets[$key]);
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
                if ($this->recordOrdering === true && empty($sets['record_ordering'])) {
                    $sets['record_ordering'] = $this->getRecordOrdering();
                }
            }

            // Process Images and Files
            $this->processImagesFiles($sets);

            if ($this->hasErrors()) {
                return false;
            }

            // Remove sets metadata
            if ($this->hasMetadata === true) {
                if (isset($sets['metadata'])) {
                    $setsMetadata = $sets['metadata'];
                    unset($sets['metadata']);
                }
            }

            // Remove sets settings
            if ($this->hasSettings === true) {
                if (isset($sets['settings'])) {
                    $setsSettings = $sets['settings'];
                    unset($sets['settings']);
                }
            }

            // Begin Transaction
            $this->db->transactionBegin();

            if ($this->qb->table($this->table)->insert($sets)) {
                $sets[$this->primaryKey] = $this->db->getLastInsertId();

                if (isset($setsMetadata)) {
                    $sets['metadata'] = new SplArrayObject();

                    foreach ($setsMetadata as $field => $value) {
                        models(Metadata::class)->insertOrUpdate($metadata = [
                            'ownership_id' => $sets[$this->primaryKey],
                            'ownership_model' => get_called_class(),
                            'name' => $field,
                            'content' => $value,
                        ], [
                            'ownership_id' => $sets[$this->primaryKey],
                            'ownership_model' => get_called_class(),
                            'name' => $field,
                        ]);

                        $sets['metadata'][$field] = $value;
                    }

                    unset($setsMetadata);
                }

                if (isset($setsSettings)) {
                    $sets['settings'] = new SplArrayObject();

                    foreach ($setsSettings as $field => $value) {
                        models(Metadata::class)->insertOrUpdate($setting = [
                            'ownership_id' => $sets[$this->primaryKey],
                            'ownership_model' => get_called_class(),
                            'key' => $field,
                            'value' => $value,
                        ], [
                            'ownership_id' => $sets[$this->primaryKey],
                            'ownership_model' => get_called_class(),
                            'name' => $field,
                        ]);

                        $sets['settings'][$field] = $value;
                    }

                    unset($setsSettings);
                }

                // After Insert Hook Process
                if ($this->db->transactionSuccess()) {
                    if (method_exists($this, 'afterInsert')) {
                        $this->afterInsert($sets);
                    } elseif (method_exists($this, 'afterInsertOrUpdate')) {
                        $this->afterInsertOrUpdate($sets);
                    }
                }

                // Rebuild Hierarchical
                if ($this->db->transactionSuccess()) {
                    if (method_exists($this, 'rebuildTree')) {
                        $this->rebuildTree();
                    }
                }

                // Commit transaction if SUCCESS
                if ($this->db->transactionSuccess() === true) {
                    $this->db->transactionCommit();

                    $label = false;
                    foreach (['name', 'label', 'title', 'code'] as $labelField) {
                        if (isset($sets[$labelField])) {
                            if (services()->has('session') and $this->flashMessage) {
                                session()->setFlash('success',
                                    language('SUCCESS_INSERT_WITH_LABEL', [$sets[$labelField]]));
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
                } else {
                    // Rollback transaction if FAILED
                    $this->db->transactionRollBack();

                    $label = false;
                    foreach (['name', 'label', 'title', 'code'] as $labelField) {
                        if (isset($sets[$labelField])) {
                            if (services()->has('session') and $this->flashMessage) {
                                session()->setFlash('danger',
                                    language('FAILED_INSERT_WITH_LABEL', [$sets[$labelField]]));
                            }

                            $this->addError(__LINE__, language('FAILED_INSERT_WITH_LABEL', [$sets[$labelField]]));

                            $label = true;
                            break;
                        }
                    }

                    if ($label === false) {
                        if (services()->has('session') and $this->flashMessage) {
                            session()->setFlash('danger', language('FAILED_INSERT'));
                        }

                        $this->addError(__LINE__, language('FAILED_INSERT'));
                    }
                }
            }
        }

        if (services()->has('session') and $this->flashMessage) {
            session()->setFlash('danger', language('FAILED_INSERT_EMPTY_DATA'));
        }

        $this->addError(__LINE__, language('FAILED_INSERT_EMPTY_DATA'));

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
            if (empty($conditions)) {
                if (empty($this->primaryKeys)) {
                    foreach ($this->primaryKeys as $primaryKey) {
                        if (isset($sets[$primaryKey])) {
                            $conditions[$primaryKey] = $sets[$primaryKey];
                        }
                    }
                } else {
                    if (isset($sets[$primaryKey])) {
                        $conditions = [$primaryKey => $sets[$primaryKey]];
                    }
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
                    if ($this->recordOrdering === true && empty($sets['record_ordering'])) {
                        $set['record_ordering'] = $this->getRecordOrdering();
                    }
                }
            }

            if (method_exists($this, 'beforeInsertMany')) {
                $this->beforeInsertMany($sets);
            } elseif (method_exists($this, 'beforeInsertOrUpdateMany')) {
                $this->beforeInsertOrUpdateMany($sets);
            }

            // Begin Transaction
            $this->db->transactionBegin();

            if ($this->qb->table($this->table)->insertBatch($sets)) {
                if ($this->db->transactionSuccess()) {
                    if (method_exists($this, 'afterInsertMany')) {
                        $this->afterInsertMany($sets);
                    } elseif (method_exists($this, 'afterInsertOrUpdateMany')) {
                        $this->afterInsertOrUpdateMany($sets);
                    }
                }

                $affectedRows = $this->db->getAffectedRows();

                if ($affectedRows > 0) {
                    if ($this->db->transactionSuccess()) {
                        if (method_exists($this, 'rebuildTree')) {
                            $this->rebuildTree();
                        }
                    }

                    if ($this->db->transactionSuccess() === true) {
                        // Commit Transaction
                        $this->db->transactionCommit();

                        if (services()->has('session') and $this->flashMessage) {
                            session()->setFlash('success', language('SUCCESS_INSERT_MANY'));
                        }

                        return $affectedRows;
                    } else {
                        // Rollback transaction if FAILED
                        $this->db->transactionRollBack();
                    }
                }
            }
        }

        if (services()->has('session') and $this->flashMessage) {
            session()->setFlash('danger', language('FAILED_INSERT_MANY'));
        }

        $this->addError(__LINE__, language('FAILED_INSERT_MANY'));

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
            if (empty($conditions)) {
                if (empty($this->primaryKeys)) {
                    foreach ($this->primaryKeys as $primaryKey) {
                        if (isset($sets[$primaryKey])) {
                            $conditions[$primaryKey] = $sets[$primaryKey];
                        }
                    }
                } else {
                    if (isset($sets[$primaryKey])) {
                        $conditions = [$primaryKey => $sets[$primaryKey]];
                    }
                }
            }

            if (count($this->fillableColumns)) {
                foreach ($sets as $key => $value) {
                    if (!in_array($key, $this->fillableColumns)) {
                        unset($sets[$key]);
                    }
                }
            }

            if (method_exists($this, 'updateRecordSets')) {
                $this->updateRecordSets($sets);
            }

            if (method_exists($this, 'beforeUpdate')) {
                $this->beforeUpdate($sets);
            } elseif (method_exists($this, 'beforeInsertOrUpdate')) {
                $this->beforeInsertOrUpdate($sets);
            }

            if (method_exists($this, 'getRecordOrdering')) {
                if ($this->recordOrdering === true && empty($sets['record_ordering'])) {
                    $sets['record_ordering'] = $this->getRecordOrdering();
                }
            }

            if ($result = $this->findWhere($conditions)) {
                if ($result->count()) {
                    $this->row = $result->first();
                } else {
                    if ($result instanceof Row) {
                        $this->row = $result;
                    }
                }

                if (empty($this->row)) {
                    if (services()->has('session') and $this->flashMessage) {
                        session()->setFlash('danger', language('FAILED_UPDATE_INVALID_DATA'));
                    }

                    return false;
                }

                // Process Images and Files
                $this->processImagesFiles($sets);

                if ($this->hasErrors()) {
                    return false;
                }

                // Remove sets metadata
                if ($this->hasMetadata === true) {
                    if (isset($sets['metadata'])) {
                        $setsMetadata = $sets['metadata'];
                        unset($sets['metadata']);
                    }
                }
                
                // Remove sets settings
                if ($this->hasSettings === true) {
                    if(isset($sets['settings'])) {
                        $setSettings = $sets['settings'];
                        unset($sets['settings']);
                    }
                }

                // Begin Transaction
                $this->db->transactionBegin();

                if ($this->qb->table($this->table)->update($sets, $conditions)) {
                    if (isset($setsMetadata)) {
                        $sets['metadata'] = new SplArrayObject();

                        foreach ($setsMetadata as $field => $value) {
                            models(Metadata::class)->insertOrUpdate($setsMetadata = [
                                'ownership_id' => $sets[$this->primaryKey],
                                'ownership_model' => get_called_class(),
                                'name' => $field,
                                'content' => $value,
                            ], [
                                'ownership_id' => $sets[$this->primaryKey],
                                'ownership_model' => get_called_class(),
                                'name' => $field,
                            ]);

                            $sets['metadata'][$field] = $value;
                        }

                        unset($setsMetadata);
                    }

                    if (isset($setsSettings)) {
                        $sets['settings'] = new SplArrayObject();

                        foreach ($setsSettings as $field => $value) {
                            models(Metadata::class)->insertOrUpdate($setting = [
                                'ownership_id' => $sets[$this->primaryKey],
                                'ownership_model' => get_called_class(),
                                'key' => $field,
                                'value' => $value,
                            ], [
                                'ownership_id' => $sets[$this->primaryKey],
                                'ownership_model' => get_called_class(),
                                'name' => $field,
                            ]);

                            $sets['settings'][$field] = $value;
                        }

                        unset($setsSettings);
                    }

                    if ($this->db->transactionSuccess()) {
                        if (method_exists($this, 'afterUpdate')) {
                            $this->afterUpdate($sets);
                        } elseif (method_exists($this, 'afterInsertOrUpdate')) {
                            $this->afterInsertOrUpdate($sets);
                        }
                    }

                    // Rebuild Hierarchical
                    if ($this->db->transactionSuccess()) {
                        if (method_exists($this, 'rebuildTree')) {
                            $this->rebuildTree();
                        }
                    }

                    // Commit transaction if SUCCESS
                    if ($this->db->transactionSuccess() === true) {
                        $this->db->transactionCommit();

                        $label = false;
                        foreach (['name', 'label', 'title', 'code'] as $labelField) {
                            if (isset($sets[$labelField])) {
                                if (services()->has('session') and $this->flashMessage) {
                                    session()->setFlash('success',
                                        language('SUCCESS_UPDATE_WITH_LABEL', [$sets[$labelField]]));
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
                    } else {
                        // Rollback transaction if FAILED
                        $this->db->transactionRollback();
                    }
                }
            }
        }

        if (services()->has('session') and $this->flashMessage) {
            session()->setFlash('danger', language('FAILED_UPDATE_EMPTY_DATA'));
        }

        $this->addError(__LINE__, language('FAILED_UPDATE_EMPTY_DATA'));

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
                $this->updateRecordSets($sets[$key]);
            }
        }

        if (method_exists($this, 'beforeUpdateMany')) {
            $this->beforeUpdateMany($sets);
        } elseif (method_exists($this, 'beforeInsertOrUpdateMany')) {
            $this->beforeInsertOrUpdateMany($sets);
        }

        // Begin Transaction
        $this->db->transactionBegin();

        if ($this->qb->table($this->table)->updateBatch($sets, $primaryKey)) {
            if ($this->db->transactionSuccess()) {
                if (method_exists($this, 'afterUpdateMany')) {
                    return $this->afterUpdateMany($sets);
                } elseif (method_exists($this, 'afterInsertOrUpdateMany')) {
                    return $this->afterInsertOrUpdateMany($sets);
                }
            }

            $affectedRows = $this->db->getAffectedRows();

            if ($this->db->transactionSuccess()) {
                if (method_exists($this, 'rebuildTree')) {
                    $this->rebuildTree();
                }
            }

            if ($this->db->transactionSuccess() === true) {
                // Commit transaction if SUCCESS
                $this->db->transactionCommit();

                if (services()->has('session') and $this->flashMessage) {
                    session()->setFlash('success', language('SUCCESS_UPDATE_MANY'));
                }

                return $affectedRows;
            } else {
                // Rollback transaction if FAILED
                $this->db->transactionRollback();
            }
        }

        if (services()->has('session') and $this->flashMessage) {
            session()->setFlash('danger', language('FAILED_UPDATE_MANY'));
        }

        $this->addError(__LINE__, language('FAILED_UPDATE_MANY'));

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
        $affectedRows = 0;
        if ($result = $this->find($id)) {
            // Begin Transaction
            $this->db->transactionBegin();

            if ($result instanceof Result) {
                foreach ($result as $row) {
                    if ($this->deleteRow($row)) {
                        $affectedRows++;
                    }
                }
            } elseif ($result instanceof Row) {
                if ($this->deleteRow($result)) {
                    $affectedRows++;
                }
            }

            if ($affectedRows > 0) {
                if ($this->db->transactionSuccess()) {
                    if (method_exists($this, 'rebuildTree')) {
                        $this->rebuildTree();
                    }
                }

                if ($this->db->transactionSuccess() === true) {
                    // Commit transaction if SUCCESS
                    $this->db->transactionCommit();

                    if (services()->has('session') and $this->flashMessage) {
                        session()->setFlash('success', language('SUCCESS_DELETE'));
                    }

                    return $affectedRows;
                } else {
                    // Rollback transaction if FAILED
                    $this->db->transactionRollback();
                }
            } else {
                // Rollback transaction if FAILED
                $this->db->transactionRollback();
            }
        }

        if (services()->has('session') and $this->flashMessage) {
            session()->setFlash('danger', language('FAILED_DELETE'));
        }

        $this->addError(__LINE__, language('FAILED_DELETE'));

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
        if (method_exists($this, 'hasChilds')) {
            if ($this->hasChilds($row->id)) {
                if (services()->has('session') and $this->flashMessage) {
                    session()->setFlash('danger', language('FAILED_DELETE_HAS_CHILD'));
                }

                return false;
            }
        }

        if (empty($this->primaryKeys)) {
            $conditions = [$this->primaryKey => $row->offsetGet($this->primaryKey)];
        } else {
            foreach ($this->primaryKeys as $key => $primaryKey) {
                $conditions[$primaryKey] = $row->offsetGet($primaryKey);
            }
        }

        if (method_exists($this, 'beforeDelete')) {
            if( ! call_user_func_array([&$this, 'beforeDelete'], [$row])) {
                return false;
            }
        }

        if ($this->qb->table($this->table)->delete($conditions)) {
            // Remove uploaded files
            $this->removeUploadedFiles($row->getArrayCopy(), $this->uploadFilePaths);

            // Delete Metadata
            if ($this->hasMetadata === true) {
                models(Metadata::class)->deleteBy([
                    'ownership_id' => $row[$this->primaryKey],
                    'ownership_model' => get_called_class(),
                ]);
            }

            // Delete Settings
            if ($this->hasSettings === true) {
                models(Settings::class)->deleteBy([
                    'ownership_id' => $row[$this->primaryKey],
                    'ownership_model' => get_called_class(),
                ]);
            }

            if ($this->db->transactionSuccess()) {
                if (method_exists($this, 'afterDelete')) {
                    call_user_func_array([&$this, 'afterDelete'], [$row]);
                }
            }

            if ($this->db->transactionSuccess()) {
                if (services()->has('session') and $this->flashMessage) {
                    session()->setFlash('success', language('SUCCESS_DELETE'));
                }

                return true;
            }
        }

        if (services()->has('session') and $this->flashMessage) {
            session()->setFlash('danger', language('FAILED_DELETE'));
        }

        $this->addError(__LINE__, language('FAILED_DELETE'));

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
                // Begin Transaction
                $this->db->transactionBegin();

                if ($result instanceof Result) {
                    foreach ($result as $row) {
                        if ($this->deleteRow($row)) {
                            $affectedRows++;
                        }
                    }
                } elseif ($result instanceof Row) {
                    if ($this->deleteRow($result)) {
                        $affectedRows++;
                    }

                }
            }

            if ($affectedRows > 0) {
                if ($this->db->transactionSuccess()) {
                    if (method_exists($this, 'rebuildTree')) {
                        $this->rebuildTree();
                    }
                }

                // Commit transaction if SUCCESS
                if ($this->db->transactionSuccess() === true) {
                    $this->db->transactionCommit();

                    if (services()->has('session') and $this->flashMessage) {
                        session()->setFlash('success', language('SUCCESS_DELETE'));
                    }

                    return $affectedRows;
                } else {
                    // Rollback transaction if FAILED
                    $this->db->transactionRollback();
                }
            } else {
                // Rollback transaction if FAILED
                $this->db->transactionRollback();
            }
        }

        if (services()->has('session') and $this->flashMessage) {
            session()->setFlash('danger', language('FAILED_DELETE'));
        }

        $this->addError(__LINE__, language('FAILED_DELETE'));

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
            // Begin Transaction
            $this->db->transactionBegin();

            foreach ($result as $row) {
                if ($this->deleteRow($row)) {
                    $affectedRows++;
                }
            }

            if ($affectedRows > 0) {
                if ($this->db->transactionSuccess()) {
                    if (method_exists($this, 'rebuildTree')) {
                        $this->rebuildTree();
                    }
                }

                // Commit transaction if SUCCESS
                if ($this->db->transactionSuccess() === true) {
                    $this->db->transactionCommit();

                    if (services()->has('session') and $this->flashMessage) {
                        session()->setFlash('success', language('SUCCESS_DELETE'));
                    }

                    return $affectedRows;
                } else {
                    // Rollback transaction if FAILED
                    $this->db->transactionRollback();
                }
            } else {
                // Rollback transaction if FAILED
                $this->db->transactionRollback();
            }
        }

        if (services()->has('session') and $this->flashMessage) {
            session()->setFlash('danger', language('FAILED_DELETE'));
        }

        $this->addError(__LINE__, language('FAILED_DELETE'));

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
     * @param array $params
     * @param string $recordStatus
     * @param string $method
     *
     * @return bool
     */
    private function updateRecordStatus(array $params, $recordStatus, $method)
    {
        $sets['record_status'] = $recordStatus;

        if (empty($this->primaryKeys)) {
            $conditions = [$this->primaryKey => reset($params)];
        } else {
            foreach ($this->primaryKeys as $key => $primaryKey) {
                $conditions[$primaryKey] = $params[$key];
            }
        }

        if (method_exists($this, 'updateRecordSets')) {
            $this->updateRecordSets($sets);
        }

        if (method_exists($this, $beforeMethod = 'before' . ucfirst($method))) {
            call_user_func_array([&$this, $beforeMethod], [$sets]);
        }

        // Begin Transaction
        $this->db->transactionBegin();

        if ($this->qb->table($this->table)->limit(1)->update($sets, $conditions)) {
            $affectedRows = $this->db->getAffectedRows();

            if ($affectedRows > 0) {
                if ($this->db->transactionSuccess()) {
                    if (method_exists($this, $afterMethod = 'after' . ucfirst($method))) {
                        call_user_func_array([&$this, $afterMethod], [$sets]);
                    }
                }

                if ($this->db->transactionSuccess()) {
                    if (method_exists($this, 'rebuildTree')) {
                        $this->rebuildTree();
                    }
                }

                if ($this->db->transactionSuccess()) {
                    // Commit transaction if SUCCESS
                    $this->db->transactionCommit();

                    $label = false;
                    foreach (['name', 'label', 'title', 'code'] as $labelField) {
                        if (isset($sets[$labelField])) {
                            if (services()->has('session') and $this->flashMessage) {
                                session()->setFlash('success',
                                    language('SUCCESS_UPDATE_WITH_LABEL', [$sets[$labelField]]));
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
                } else {
                    // Rollback transaction if FAILED
                    $this->db->transactionRollback();
                }
            } else {
                // Rollback transaction if FAILED
                $this->db->transactionRollback();
            }
        }

        if (services()->has('session') and $this->flashMessage) {
            session()->setFlash('danger', language('FAILED_UPDATE'));
        }

        $this->addError(__LINE__, language('FAILED_UPDATE'));

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::updateRecordStatusMany
     *
     * @param array $ids
     * @param string $recordStatus
     * @param string $method
     *
     * @return bool|int
     */
    private function updateRecordStatusMany(array $ids, $recordStatus, $method)
    {
        if (count($ids)) {
            $sets['record_status'] = $recordStatus;
            $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';

            $this->qb->whereIn($primaryKey, $ids);

            if (method_exists($this, 'updateRecordSets')) {
                $this->updateRecordSets($sets);
            }

            if (method_exists($this, $beforeMethod = 'before' . ucfirst($method))) {
                call_user_func_array([&$this, $beforeMethod], [$sets]);
            }

            // Begin Transaction
            $this->db->transactionBegin();

            if ($this->qb->table($this->table)->update($sets)) {
                $affectedRows = $this->db->getAffectedRows();

                if ($affectedRows > 0) {
                    if ($this->db->transactionSuccess()) {
                        if (method_exists($this, $afterMethod = 'after' . ucfirst($method))) {
                            call_user_func_array([&$this, $afterMethod], [$sets]);
                        }
                    }

                    if ($this->db->transactionSuccess()) {
                        if (method_exists($this, 'rebuildTree')) {
                            $this->rebuildTree();
                        }
                    }

                    if ($this->db->transactionSuccess()) {
                        // Commit transaction if SUCCESS
                        $this->db->transactionCommit();

                        if (services()->has('session') and $this->flashMessage) {
                            session()->setFlash('success', language('SUCCESS_UPDATE_MANY'));
                        }

                        return $affectedRows;
                    } else {
                        // Rollback transaction if FAILED
                        $this->db->transactionRollback();
                    }
                } else {
                    // Rollback transaction if FAILED
                    $this->db->transactionRollback();
                }
            }
        }

        if (services()->has('session') and $this->flashMessage) {
            session()->setFlash('danger', language('FAILED_UPDATE_MANY'));
        }

        $this->addError(__LINE__, language('FAILED_UPDATE_MANY'));

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::updateRecordStatusBy
     *
     * @param string $recordStatus
     * @param string $method
     * @param array $conditions
     *
     * @return bool|int
     */
    private function updateRecordStatusBy($recordStatus, $method, array $conditions)
    {
        if (count($conditions)) {
            $sets['record_status'] = $recordStatus;

            if (method_exists($this, 'updateRecordSets')) {
                $this->updateRecordSets($sets);
            }

            if (method_exists($this, $beforeMethod = 'before' . ucfirst($method))) {
                call_user_func_array([&$this, $beforeMethod], [$sets]);
            }

            // Begin transaction
            $this->db->transactionBegin();

            if ($this->qb->table($this->table)->update($sets, $conditions)) {

                $affectedRows = $this->db->getAffectedRows();

                if ($affectedRows > 0) {
                    if ($this->db->transactionSuccess()) {
                        if (method_exists($this, $afterMethod = 'after' . ucfirst($method))) {
                            call_user_func_array([&$this, $afterMethod], [$sets]);
                        }
                    }

                    if ($this->db->transactionSuccess()) {
                        if (method_exists($this, 'rebuildTree')) {
                            $this->rebuildTree();
                        }
                    }

                    if ($this->db->transactionSuccess()) {
                        // Commit transaction if SUCCESS
                        $this->db->transactionCommit();

                        if (services()->has('session') and $this->flashMessage) {
                            session()->setFlash('success', language('SUCCESS_UPDATE'));
                        }

                        return $affectedRows;
                    } else {
                        // Rollback transaction if FAILED
                        $this->db->transactionRollback();
                    }
                } else {
                    // Rollback transaction if FAILED
                    $this->db->transactionRollback();
                }
            }
        }

        if (services()->has('session') and $this->flashMessage) {
            session()->setFlash('danger', language('FAILED_UPDATE'));
        }

        $this->addError(__LINE__, language('FAILED_UPDATE'));

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::updateRecordStatusManyBy
     *
     * @param string $recordStatus
     * @param string $method
     * @param array $conditions
     *
     * @return bool|int
     */
    private function updateRecordStatusManyBy($recordStatus, $method, array $conditions)
    {
        if (count($conditions)) {
            $sets['record_status'] = $recordStatus;

            if (method_exists($this, 'updateRecordSets')) {
                $this->updateRecordSets($sets);
            }

            if (method_exists($this, $beforeMethod = 'before' . ucfirst($method))) {
                call_user_func_array([&$this, $beforeMethod], [$sets]);
            }

            // Begin transaction
            $this->db->transactionBegin();

            if ($this->qb->table($this->table)->update($sets, $conditions)) {
                $affectedRows = $this->db->getAffectedRows();

                if ($affectedRows > 0) {
                    if ($this->db->transactionSuccess()) {
                        if (method_exists($this, $afterMethod = 'after' . ucfirst($method))) {
                            call_user_func_array([&$this, $afterMethod], [$sets]);
                        }
                    }

                    if ($this->db->transactionSuccess()) {
                        if (method_exists($this, 'rebuildTree')) {
                            $this->rebuildTree();
                        }
                    }

                    if ($this->db->transactionSuccess()) {
                        // Commit transaction if SUCCESS
                        $this->db->transactionCommit();

                        if (services()->has('session') and $this->flashMessage) {
                            session()->setFlash('success', language('SUCCESS_UPDATE'));
                        }

                        return $affectedRows;
                    } else {
                        // Rollback transaction if FAILED
                        $this->db->transactionRollback();
                    }
                } else {
                    // Rollback transaction if FAILED
                    $this->db->transactionRollback();
                }
            }
        }

        if (services()->has('session') and $this->flashMessage) {
            session()->setFlash('danger', language('FAILED_UPDATE_MANY'));
        }

        $this->addError(__LINE__, language('FAILED_UPDATE_MANY'));

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
        return $this->updateRecordStatus(func_get_args(), 'PUBLISH', 'publish');
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
        return $this->updateRecordStatus(func_get_args(), 'PUBLISH', 'restore');
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
        return $this->updateRecordStatus(func_get_args(), 'UNPUBLISH', 'unpublish');
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
        return $this->updateRecordStatus(func_get_args(), 'DELETED', 'softDelete');
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
        return $this->updateRecordStatus(func_get_args(), 'ARCHIVED', 'archive');
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
        return $this->updateRecordStatus(func_get_args(), 'LOCKED', 'lock');
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
        return $this->updateRecordStatus(func_get_args(), 'DRAFT', 'draft');
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