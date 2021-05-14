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

use O2System\Framework\Models\Sql\System\Metadata;
use O2System\Framework\Models\Sql\System\Settings;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Framework\Models\Sql\DataObjects\Result;
use O2System\Framework\Models\Sql\DataObjects\Result\Row;
use O2System\Kernel\DataStructures\Input\Abstracts\AbstractInput;
use O2System\Kernel\DataStructures\Input\Data;
use O2System\Kernel\Http\Message\UploadFile;
use O2System\Spl\DataStructures\SplArrayObject;
use O2System\Spl\DataStructures\SplArrayStorage;

/**
 * Class ModifierTrait
 *
 * @package O2System\Framework\Models\Sql\Traits
 */
trait ModifierTrait
{
    /**
     * ModifierTrait::$insertValidationRules
     *
     * @var array
     */
    public $insertValidationRules = [];

    /**
     * ModifierTrait::$insertValidationCustomErrors
     *
     * @var array
     */
    public $insertValidationCustomErrors = [];

    /**
     * ModifierTrait::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [];

    /**
     * ModifierTrait::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [];

    /**
     * ModifierTrait::$actionValidationRules
     *
     * @var array
     */
    public $actionValidationRules = [];

    /**
     * ModifierTrait::$actionValidationCustomErrors
     *
     * @var array
     */
    public $actionValidationCustomErrors = [];

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
    public function flashMessage($enabled): self
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
     * @param array $data
     * @return array
     */
    private function getUploadedFiles(array $files, $uploadFilePaths, array &$data)
    {
        $files = array_intersect_key($files, $uploadFilePaths);
        foreach ($files as $field => &$file) {
            if ($file instanceof UploadFile) {
                $file->setPath($uploadFilePaths[$field]);
                if ($file->store()) {
                    $data[$field] = $file->getClientFilename();

                    if (isset($this->row[$field])) {
                        if ($data[$field] !== $this->row[$field]) {
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
                $file = $this->getUploadedFiles($file, $uploadFilePaths[$field], $data);
                foreach ($file as $fileKey => $fileObject) {
                    if ($fileObject instanceof UploadFile) {
                        if (!$fileObject->isMoved) {
                            $fileObject->setPath($uploadFilePaths[$field][$fileKey]);
                            if ($fileObject->store()) {
                                $data[$field][$fileKey] = $fileObject->getClientFilename();

                                if (isset($this->row[$field][$fileKey])) {
                                    if ($data[$field][$fileKey] !== $this->row[$field][$fileKey]) {
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
                            if (isset($data[$fileKey])) {
                                unset($data[$fileKey]);
                            }

                            $data[$field][$fileKey] = $fileObject->getClientFilename();

                            if (isset($this->row[$field][$fileKey])) {
                                if ($data[$field][$fileKey] !== $this->row[$field][$fileKey]) {
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
     * @param array $row
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
     * @param array $data
     */
    protected function processImagesFiles(&$data)
    {
        if ($files = input()->files() and count($this->uploadFilePaths)) {
            $this->uploadedFiles = $this->getUploadedFiles($files->getArrayCopy(), $this->uploadFilePaths, $data);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::insert
     *
     * @param SplArrayStorage $data
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     */
    public function insert(SplArrayStorage $data)
    {
        if($data instanceof AbstractInput) {
            if (count($this->insertValidationRules)) {
                $data->validation($this->insertValidationRules, $this->insertValidationCustomErrors);

                if (!$data->validate()) {
                    $this->addErrors($data->validator->getErrors());

                    if (services()->has('session') and $this->flashMessage) {
                        $errors = new Unordered();
                        foreach ($data->validator->getErrors() as $error) {
                            $errors->createList($error);
                        }

                        session()->setFlash('danger',
                            language('FAILED_INSERT', $errors->__toString()));
                    }

                    return false;
                }
            }
        }

        if (count($data)) {
            if (method_exists($this, 'insertRecordData')) {
                $this->insertRecordData($data);
            }

            if (method_exists($this, 'beforeInsertOrUpdate')) {
                $this->beforeInsertOrUpdate($data);
            }

            if (method_exists($this, 'beforeInsert')) {
                $this->beforeInsert($data);
            }

            if (method_exists($this, 'getRecordOrdering')) {
                if ($this->recordOrdering === true && empty($data['record_ordering'])) {
                    $data['record_ordering'] = $this->getRecordOrdering();
                }
            }

            // Process Images and Files
            $this->processImagesFiles($data);

            if ($this->hasErrors()) {
                return false;
            }

            $temporaryData = [];

            // Remove data metadata
            if ($this->hasMetadata === true) {
                if (isset($data['metadata'])) {
                    $temporaryData['metadata'] = new Data($data['metadata']);
                    unset($data['metadata']);
                }
            }

            // Remove data settings
            if ($this->hasSettings === true) {
                if (isset($data['settings'])) {
                    $temporaryData['settings'] = new Data($data['settings']);
                    unset($data['settings']);
                }
            }

            // Remove unfillable data
            if (count($this->fillableColumns)) {
                $unfillableColumns = array_diff($data->getKeys(), $this->fillableColumns);
                foreach ($unfillableColumns as $unfillableColumn) {
                    $temporaryData[$unfillableColumn] = $data->offsetGet($unfillableColumn);
                    $data->offsetUnset($unfillableColumn);
                }
            }

            // Begin Transaction
            $this->db->transactionBegin();

            if ($this->qb->table($this->table)->insert($data->getArrayCopy())) {
                $data[$this->primaryKey] = $this->db->getLastInsertId();

                if (!empty($temporaryData)) {
                    $data->append($temporaryData);
                }

                if (isset($temporaryData['metadata'])) {
                    if (empty($this->primaryKeys)) {
                        $ownershipId = $data[$this->primaryKey];
                    } else {
                        $ownershipId = [];
                        foreach ($this->primaryKeys as $primaryKey) {
                            array_push($ownershipId, $data[$primaryKey]);
                        }

                        $ownershipId = implode('-', $ownershipId);
                    }

                    foreach ($temporaryData['metadata'] as $field => $value) {
                        models(Metadata::class)->insertOrUpdate(new Data([
                            'ownership_id' => $ownershipId,
                            'ownership_model' => get_called_class(),
                            'name' => $field,
                            'content' => $value,
                        ]), [
                            'ownership_id' => $ownershipId,
                            'ownership_model' => get_called_class(),
                            'name' => $field,
                        ]);
                    }
                }

                if (isset($temporaryData['settings'])) {
                    if (empty($this->primaryKeys)) {
                        $ownershipId = $data[$this->primaryKey];
                    } else {
                        $ownershipId = [];
                        foreach ($this->primaryKeys as $primaryKey) {
                            array_push($ownershipId, $data[$primaryKey]);
                        }

                        $ownershipId = implode('-', $ownershipId);
                    }

                    foreach ($temporaryData['settings'] as $field => $value) {
                        models(Metadata::class)->insertOrUpdate(new Data([
                            'ownership_id' => $ownershipId,
                            'ownership_model' => get_called_class(),
                            'key' => $field,
                            'value' => $value,
                        ]), [
                            'ownership_id' => $ownershipId,
                            'ownership_model' => get_called_class(),
                            'name' => $field,
                        ]);
                    }
                }

                unset($temporaryData);

                if(count($this->primaryKeys)) {
                    foreach ($this->primaryKeys as $primaryKey) {
                        if($data->offsetExists($primaryKey)) {
                            $conditions[$primaryKey] = $data->offsetGet($primaryKey);
                        }
                    }
                } else {
                    if($data->offsetExists($this->primaryKey)) {
                        $conditions[$this->primaryKey] = $data->offsetGet($this->primaryKey);
                    }
                }

                if ($result = $this->findWhere($conditions)) {
                    $this->row = null;

                    if ($result->count()) {
                        $this->row = $result->first();
                    } elseif ($result instanceof Row) {
                        $this->row = $result;
                    }
                }

                // After Insert Hook Process
                if ($this->db->transactionSuccess()) {
                    foreach(['afterInsertOrUpdate', 'afterInsert'] as $afterInsertMethod) {
                        if (method_exists($this, $afterInsertMethod)) {
                            $parameterName = null;
                            $reflectionMethod = new \ReflectionMethod($this, $afterInsertMethod);

                            if(method_exists($reflectionMethod->getParameters()[0]->getType(), 'getName')) {
                                $parameterName = $reflectionMethod->getParameters()[0]->getType()->getName();
                            }


                            if($parameterName === 'O2System\Database\DataObjects\Result\Row') {
                                call_user_func_array([&$this, $afterInsertMethod], [$this->row]);
                            } else {
                                call_user_func_array([&$this, $afterInsertMethod], [$data]);
                            }
                        }
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
                        if (isset($data[$labelField])) {
                            if (services()->has('session') and $this->flashMessage) {
                                session()->setFlash('success',
                                    language('SUCCESS_INSERT_WITH_LABEL', [$data[$labelField]]));
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
                        if (isset($data[$labelField])) {
                            if (services()->has('session') and $this->flashMessage) {
                                session()->setFlash('danger',
                                    language('FAILED_INSERT_WITH_LABEL', [$data[$labelField]]));
                            }

                            $this->addError(__LINE__, language('FAILED_INSERT_WITH_LABEL', [$data[$labelField]]));

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
            } else {
                if (services()->has('session') and $this->flashMessage) {
                    session()->setFlash('danger', $this->db->getLastErrorMessage());
                }

                $this->addError($this->db->getLastErrorCode(), $this->db->getLastErrorMessage());

                return false;
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
     * @param SplArrayStorage $data
     * @param array $conditions
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     */
    public function insertOrUpdate(SplArrayStorage $data, array $conditions = [])
    {
        if ($data->count()) {
            if (empty($conditions)) {
                if (empty($this->primaryKeys)) {
                    foreach ($this->primaryKeys as $primaryKey) {
                        if ($data->offsetExists($primaryKey)) {
                            $conditions[$primaryKey] = $data->offsetGet($primaryKey);
                        }
                    }
                } else {
                    if ($data->offsetExists($this->primaryKey)) {
                        $conditions = [$this->primaryKey => $data->offsetGet($this->primaryKey)];
                    }
                }
            }

            // Try to find
            if ($result = $this->qb->from($this->table)->getWhere($conditions)) {
                if ($result->count() > 0) {
                    if($data instanceof AbstractInput) {
                        if (count($this->updateValidationRules)) {
                            $data->validation($this->updateValidationRules, $this->updateValidationCustomErrors);

                            if (!$data->validate()) {
                                $this->addErrors($data->validator->getErrors());

                                if (services()->has('session') and $this->flashMessage) {
                                    $errors = new Unordered();
                                    foreach ($data->validator->getErrors() as $error) {
                                        $errors->createList($error);
                                    }

                                    session()->setFlash('danger',
                                        language('FAILED_INSERT', $errors->__toString()));
                                }

                                return false;
                            }
                        }
                    }

                    return $this->update($data, $conditions);
                } else {
                    if($data instanceof AbstractInput) {
                        if (count($this->insertValidationRules)) {
                            $data->validation($this->insertValidationRules, $this->insertValidationCustomErrors);

                            if (!$data->validate()) {
                                $this->addErrors($data->validator->getErrors());

                                if (services()->has('session') and $this->flashMessage) {
                                    $errors = new Unordered();
                                    foreach ($data->validator->getErrors() as $error) {
                                        $errors->createList($error);
                                    }

                                    session()->setFlash('danger',
                                        language('FAILED_INSERT', $errors->__toString()));
                                }

                                return false;
                            }
                        }
                    }

                    return $this->insert($data);
                }
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::insertMany
     *
     * @param array $data
     *
     * @return bool|int
     */
    public function insertMany(array $data)
    {
        if (count($data)) {
            if (method_exists($this, 'insertRecordData')) {
                foreach ($data as $set) {
                    $this->insertRecordData($set);
                    if ($this->recordOrdering === true && empty($data['record_ordering'])) {
                        $set['record_ordering'] = $this->getRecordOrdering();
                    }
                }
            }

            if (method_exists($this, 'beforeInsertOrUpdateMany')) {
                $this->beforeInsertOrUpdateMany($data);
            }

            if (method_exists($this, 'beforeInsertMany')) {
                $this->beforeInsertMany($data);
            }

            // Begin Transaction
            $this->db->transactionBegin();

            if ($this->qb->table($this->table)->insertBatch($data)) {
                if ($this->db->transactionSuccess()) {
                    if (method_exists($this, 'afterInsertOrUpdateMany')) {
                        $this->afterInsertOrUpdateMany($data);
                    }

                    if (method_exists($this, 'afterInsertMany')) {
                        $this->afterInsertMany($data);
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
            } else {
                if (services()->has('session') and $this->flashMessage) {
                    session()->setFlash('danger', $this->db->getLastErrorMessage());
                }

                $this->addError($this->db->getLastErrorCode(), $this->db->getLastErrorMessage());
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::insertIfNotExists
     *
     * @param SplArrayStorage $data
     * @param array $conditions
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     */
    public function insertIfNotExists(SplArrayStorage $data, array $conditions = [])
    {
        if($data instanceof AbstractInput) {
            if (count($this->insertValidationRules)) {
                $data->validation($this->insertValidationRules, $this->insertValidationCustomErrors);

                if (!$data->validate()) {
                    $this->addErrors($data->validator->getErrors());

                    if (services()->has('session') and $this->flashMessage) {
                        $errors = new Unordered();
                        foreach ($data->validator->getErrors() as $error) {
                            $errors->createList($error);
                        }

                        session()->setFlash('danger',
                            language('FAILED_INSERT', $errors->__toString()));
                    }

                    return false;
                }
            }
        }

        if (empty($conditions)) {
            $conditions = $data->getArrayCopy();
        }

        if (count($data)) {
            if ($result = $this->qb->from($this->table)->getWhere($conditions)) {
                if ($result->count() == 0) {
                    return $this->insert($data);
                }
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::update
     *
     * @param SplArrayStorage $data
     * @param array $conditions
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     */
    public function update(SplArrayStorage $data, array $conditions = [])
    {
        if(empty($conditions)) {
            if(count($this->primaryKeys)) {
                foreach ($this->primaryKeys as $primaryKey) {
                    if($data->offsetExists($primaryKey)) {
                        $conditions[$primaryKey] = $data->offsetGet($primaryKey);
                    }

                    if(empty($this->updateValidationRules)) {
                        $this->updateValidationRules[$primaryKey] = 'required';
                        $this->updateValidationCustomErrors[$primaryKey] = [
                            'required' => language('LABEL_' . strtoupper($primaryKey)) . ' cannot be empty!',
                        ];
                    }
                }
            } else {
                if($data->offsetExists($this->primaryKey)) {
                    $conditions[$this->primaryKey] = $data->offsetGet($this->primaryKey);
                }

                if(empty($this->updateValidationRules)) {
                    $this->updateValidationRules[$this->primaryKey] = 'required';
                    $this->updateValidationCustomErrors[$this->primaryKey] = [
                        'required' => language('LABEL_' . strtoupper($this->primaryKey)) . ' cannot be empty!',
                    ];
                }
            }
        }

        if($data instanceof AbstractInput) {
            if (count($this->updateValidationRules)) {
                $data->validation($this->updateValidationRules, $this->updateValidationCustomErrors);

                if (!$data->validate()) {
                    $this->addErrors($data->validator->getErrors());

                    if (services()->has('session') and $this->flashMessage) {
                        $errors = new Unordered();
                        foreach ($data->validator->getErrors() as $error) {
                            $errors->createList($error);
                        }

                        session()->setFlash('danger',
                            language('FAILED_UPDATE', $errors->__toString()));
                    }

                    return false;
                }
            }
        }

        if (count($data)) {
            if (method_exists($this, 'updateRecordData')) {
                $this->updateRecordData($data);
            }

            if (method_exists($this, 'beforeInsertOrUpdate')) {
                $this->beforeInsertOrUpdate($data);
            }

            if (method_exists($this, 'beforeUpdate')) {
                $this->beforeUpdate($data);
            }

            if (method_exists($this, 'getRecordOrdering')) {
                if ($this->recordOrdering === true && empty($data['record_ordering'])) {
                    $data['record_ordering'] = $this->getRecordOrdering();
                }
            }
            if ($result = $this->findWhere($conditions)) {
                $this->row = null;

                if ($result->count()) {
                    $this->row = $result->first();
                } else {
                    if ($result instanceof Row) {
                        $this->row = $result;
                    }
                }

                if (empty($this->row)) {
                    if (services()->has('session') and $this->flashMessage) {
                        session()->setFlash('danger', language('FAILED_DATA_NOT_FOUND'));
                    }

                    $this->addError(__LINE__, language('FAILED_DATA_NOT_FOUND'));
                    return false;
                } else {
                    $this->row->merge($data->getArrayCopy());
                }

                // Process Images and Files
                $this->processImagesFiles($data);

                if ($this->hasErrors()) {
                    return false;
                }

                $temporaryData = [];

                // Remove data metadata
                if ($this->hasMetadata === true) {
                    if (isset($data['metadata'])) {
                        $this->row->metadata->merge($data['metadata']);
                        $temporaryData['metadata'] = $this->row->metadata->getArrayCopy();
                        unset($data['metadata']);
                    }
                }

                // Remove data settings
                if ($this->hasSettings === true) {
                    if (isset($data['settings'])) {
                        $this->row->settings->merge($data['settings']);
                        $temporaryData['settings'] = $this->row->settings->getArrayCopy();
                        unset($data['settings']);
                    }
                }

                // Remove unfillable data
                if (count($this->fillableColumns)) {
                    $unfillableColumns = array_diff($data->getKeys(), $this->fillableColumns);
                    foreach ($unfillableColumns as $unfillableColumn) {
                        $temporaryData[$unfillableColumn] = $data->offsetGet($unfillableColumn);
                        $data->offsetUnset($unfillableColumn);
                    }
                }

                // Begin Transaction
                $this->db->transactionBegin();

                if ($this->qb->table($this->table)->limit(1)->update($data->getArrayCopy(), $conditions)) {

                    if (isset($temporaryData['metadata'])) {
                        if (empty($this->primaryKeys)) {
                            if(isset($this->row[$this->primaryKey])) {
                                $ownershipId = $this->row[$this->primaryKey];
                            } else {
                                $appendData = $this->findWhere($conditions, 1);
                                $this->row->append($appendData->getArrayCopy());
                                $ownershipId = $this->row->offsetGet($primaryKey);
                            }

                        } else {
                            $ownershipId = [];
                            foreach ($this->primaryKeys as $primaryKey) {
                                $ownershipId[] = $this->row->offsetGet($primaryKey);
                            }

                            $ownershipId = implode('-', $ownershipId);
                        }

                        foreach ($temporaryData['metadata'] as $field => $value) {
                            models(Metadata::class)->insertOrUpdate(new Data([
                                'ownership_id' => $ownershipId,
                                'ownership_model' => get_called_class(),
                                'name' => $field,
                                'content' => $value,
                            ]), [
                                'ownership_id' => $ownershipId,
                                'ownership_model' => get_called_class(),
                                'name' => $field,
                            ]);
                        }
                    }

                    if (isset($temporaryData['settings'])) {
                        if (empty($this->primaryKeys)) {
                            if(isset($this->row[$this->primaryKey])) {
                                $ownershipId = $this->row[$this->primaryKey];
                            } else {
                                $appendData = $this->findWhere($conditions, 1);
                                $this->row->append($appendData->getArrayCopy());
                                $ownershipId = $this->row->offsetGet($primaryKey);
                            }

                        } else {
                            $ownershipId = [];
                            foreach ($this->primaryKeys as $primaryKey) {
                                $ownershipId[] = $this->row->offsetGet($primaryKey);
                            }

                            $ownershipId = implode('-', $ownershipId);
                        }

                        foreach ($temporaryData['settings'] as $field => $value) {
                            models(Settings::class)->insertOrUpdate(new Data([
                                'ownership_id' => $ownershipId,
                                'ownership_model' => get_called_class(),
                                'key' => $field,
                                'value' => $value,
                            ]), [
                                'ownership_id' => $ownershipId,
                                'ownership_model' => get_called_class(),
                                'key' => $field,
                            ]);
                        }
                    }

                    unset($temporaryData);

                    // After Insert Hook Process
                    if ($this->db->transactionSuccess()) {
                        foreach(['afterInsertOrUpdate', 'afterUpdate'] as $afterInsertMethod) {
                            if (method_exists($this, $afterInsertMethod)) {
                                $parameterName = null;
                                $reflectionMethod = new \ReflectionMethod($this, $afterInsertMethod);

                                if(method_exists($reflectionMethod->getParameters()[0]->getType(), 'getName')) {
                                    $parameterName = $reflectionMethod->getParameters()[0]->getType()->getName();
                                }


                                if($parameterName === 'O2System\Database\DataObjects\Result\Row') {
                                    call_user_func_array([&$this, $afterInsertMethod], [$this->row]);
                                } else {
                                    call_user_func_array([&$this, $afterInsertMethod], [$data]);
                                }
                            }
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
                            if (isset($data[$labelField])) {
                                if (services()->has('session') and $this->flashMessage) {
                                    session()->setFlash('success',
                                        language('SUCCESS_UPDATE_WITH_LABEL', [$this->row[$labelField]]));
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
                    if (services()->has('session') and $this->flashMessage) {
                        session()->setFlash('danger', $this->db->getLastErrorMessage());
                    }

                    $this->addError($this->db->getLastErrorCode(), $this->db->getLastErrorMessage());

                    return false;
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
     * @param SplArrayStorage $data
     * @param array $conditions
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     */
    public function updateOrInsert(SplArrayStorage $data, array $conditions = [])
    {
        return $this->insertOrUpdate($data, $conditions);
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::updateMany
     *
     * @param SplArrayStorage $data
     * @param array           $conditions
     *
     * @return bool|array
     */
    public function updateMany(SplArrayStorage $data, array $conditions = [])
    {
        $this->updateRecordData($data);

        if (method_exists($this, 'beforeInsertOrUpdateMany')) {
            $this->beforeInsertOrUpdateMany($data);
        }

        if (method_exists($this, 'beforeUpdateMany')) {
            $this->beforeUpdateMany($data);
        }

        // Begin Transaction
        $this->db->transactionBegin();

        if ($this->qb->table($this->table)->update($data, $conditions)) {
            if ($this->db->transactionSuccess()) {
                if (method_exists($this, 'afterInsertOrUpdateMany')) {
                    return $this->afterInsertOrUpdateMany($data);
                }

                if (method_exists($this, 'afterUpdateMany')) {
                    return $this->afterUpdateMany($data);
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
                    session()->setFlash('success', language('SUCCESS_UPDATE_MANY', [$affectedRows]));
                }

                return $affectedRows;
            } else {
                // Rollback transaction if FAILED
                $this->db->transactionRollback();
            }
        } else {
            if (services()->has('session') and $this->flashMessage) {
                session()->setFlash('danger', $this->db->getLastErrorMessage());
            }

            $this->addError($this->db->getLastErrorCode(), $this->db->getLastErrorMessage());
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

        if(count($params)) {
            $conditions = [];

            if(empty($this->primaryKeys)) {
                $conditions[$this->primaryKey] = reset($params);
            } else {
                foreach($this->primaryKeys as $key => $primaryKey) {
                    if(isset($params[$key])) {
                        $conditions[$primaryKey] = $params[$key];
                    }
                }
            }
        }

        $affectedRows = 0;
        if ($result = $this->findWhere($conditions, 1)) {
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
            session()->setFlash('danger', language('DATA_NOT_FOUND'));
        }

        $this->addError(404, language('DATA_NOT_FOUND'));

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

                $this->addError(403, language('FAILED_DELETE_HAS_CHILD', [$row]));

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
            call_user_func_array([&$this, 'beforeDelete'], [$row]);

            if($this->hasErrors()) {
                if (services()->has('session') and $this->flashMessage) {
                    session()->setFlash('danger', $this->db->getLastErrorMessage());
                }

                return false;
            }
        }

        if ($this->qb->table($this->table)->limit(1)->delete($conditions)) {
            // Remove uploaded files
            $this->removeUploadedFiles($row->getArrayCopy(), $this->uploadFilePaths);

            if (empty($this->primaryKeys)) {
                $ownershipId = $data[$this->primaryKey];
            } else {
                $ownershipId = [];
                foreach ($this->primaryKeys as $primaryKey) {
                    array_push($ownershipId, $row->offsetGet($primaryKey));
                }

                $ownershipId = implode('-', $ownershipId);
            }

            // Delete Metadata
            if ($this->hasMetadata === true) {
                models(Metadata::class)->deleteBy([
                    'ownership_id' => $ownershipId,
                    'ownership_model' => get_called_class(),
                ]);
            }

            // Delete Settings
            if ($this->hasSettings === true) {
                models(Settings::class)->deleteBy([
                    'ownership_id' => $ownershipId,
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
        } else {
            if (services()->has('session') and $this->flashMessage) {
                session()->setFlash('danger', $this->db->getLastErrorMessage());
            }

            $this->addError($this->db->getLastErrorCode(), $this->db->getLastErrorMessage());
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
            $affectedRows = 0;
            if ($result = $this->findWhere($conditions, 1)) {
                // Begin Transaction
                $this->db->transactionBegin();

                if ($result instanceof Result) {
                    if ($this->deleteRow($result->first())) {
                        $affectedRows++;
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
                        session()->setFlash('success', language('SUCCESS_DELETE_MANY', [$affectedRows]));
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
            session()->setFlash('danger', language('FAILED_DELETE_MANY'));
        }

        $this->addError(__LINE__, language('FAILED_DELETE_MANY'));

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
        if (count($conditions)) {
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
                        session()->setFlash('success', language('SUCCESS_DELETE_MANY_BY', [$conditions]));
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
            session()->setFlash('danger', language('FAILED_DELETE_MANY_BY', [$conditions]));
        }

        $this->addError(__LINE__, language('FAILED_DELETE_MANY_BY', [$conditions]));

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * ModifierTrait::updateRecordStatus
     *
     * @param array  $primaryKeys
     * @param string $recordStatus
     * @param string $method
     *
     * @return bool
     */
    public function updateRecordStatus(array $params, $recordStatus, $method)
    {
        $conditions = [];

        if(empty($this->primaryKeys)) {
            $conditions[$this->primaryKey] = reset($params);
        } else {
            foreach($this->primaryKeys as $key => $primaryKey) {
                if(isset($params[$key])) {
                    $conditions[$primaryKey] = $params[$key];
                }
            }
        }

        return $this->updateRecordStatusBy($recordStatus, $method, $conditions);
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
            $data['record_status'] = $recordStatus;
            $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';

            $this->qb->whereIn($primaryKey, $ids);

            if (method_exists($this, 'updateRecordData')) {
                $this->updateRecordData($data);
            }

            if (method_exists($this, $beforeMethod = 'before' . ucfirst($method))) {
                call_user_func_array([&$this, $beforeMethod], [$data]);
            }

            // Begin Transaction
            $this->db->transactionBegin();

            if ($this->qb->table($this->table)->update($data)) {
                $affectedRows = $this->db->getAffectedRows();

                if ($affectedRows > 0) {
                    if ($this->db->transactionSuccess()) {
                        if (method_exists($this, $afterMethod = 'after' . ucfirst($method))) {
                            call_user_func_array([&$this, $afterMethod], [$data]);
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
                            session()->setFlash('success', language('SUCCESS_UPDATE_MANY', [$affectedRows]));
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
            } else {
                if (services()->has('session') and $this->flashMessage) {
                    session()->setFlash('danger', $this->db->getLastErrorMessage());
                }

                $this->addError($this->db->getLastErrorCode(), $this->db->getLastErrorMessage());
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
        if($result = $this->findWhere($conditions)) {
            if ($result instanceof Row) {
                $data = $result;
            } elseif ($result instanceof Result) {
                if($result->count() == 1) {
                    $data = $result->first();
                }
            }

            unset($result);

            if(isset($data)) {
                // Begin Transaction
                $this->db->transactionBegin();

                if($this->qb->table($this->table)->limit(1)->update([
                    'record_status' => $recordStatus
                ], $conditions)) {
                    $affectedRows = $this->db->getAffectedRows();

                    if ($affectedRows > 0) {
                        if ($this->db->transactionSuccess()) {
                            // Commit transaction if SUCCESS
                            $this->db->transactionCommit();

                            $label = false;
                            foreach (['name', 'label', 'title', 'code'] as $labelField) {
                                if (isset($data[$labelField])) {
                                    if (services()->has('session') and $this->flashMessage) {
                                        session()->setFlash('success',
                                            language('SUCCESS_UPDATE_WITH_LABEL', [$data[$labelField]]));
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
                } else {
                    if (services()->has('session') and $this->flashMessage) {
                        session()->setFlash('danger', $this->db->getLastErrorMessage());
                    }

                    $this->addError($this->db->getLastErrorCode(), $this->db->getLastErrorMessage());
                }
            }
        }

        if (services()->has('session') and $this->flashMessage) {
            session()->setFlash('danger', language('DATA_NOT_FOUND'));
        }

        $this->addError(404, language('DATA_NOT_FOUND'));

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
            $data['record_status'] = $recordStatus;

            if (method_exists($this, 'updateRecordData')) {
                $this->updateRecordData($data);
            }

            if (method_exists($this, $beforeMethod = 'before' . ucfirst($method))) {
                call_user_func_array([&$this, $beforeMethod], [$data]);
            }

            // Begin transaction
            $this->db->transactionBegin();

            if ($this->qb->table($this->table)->update($data, $conditions)) {
                $affectedRows = $this->db->getAffectedRows();

                if ($affectedRows > 0) {
                    if ($this->db->transactionSuccess()) {
                        if (method_exists($this, $afterMethod = 'after' . ucfirst($method))) {
                            call_user_func_array([&$this, $afterMethod], [$data]);
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
                            session()->setFlash('success', language('SUCCESS_UPDATE_MANY_BY', [$affectedRows, $conditions]));
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
            } else {
                if (services()->has('session') and $this->flashMessage) {
                    session()->setFlash('danger', $this->db->getLastErrorMessage());
                }

                $this->addError($this->db->getLastErrorCode(), $this->db->getLastErrorMessage());
            }
        }

        if (services()->has('session') and $this->flashMessage) {
            session()->setFlash('danger', language('FAILED_UPDATE_MANY', [$conditions]));
        }

        $this->addError(__LINE__, language('FAILED_UPDATE_MANY', $conditions));

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
