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

namespace O2System\Framework\Models\Sql\Relations\Maps\Abstracts;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;

/**
 * Class AbstractMap
 * @package O2System\Framework\Models\Sql\Relations\Maps\Abstracts
 */
abstract class AbstractMap
{
    /**
     * AbstractMap::$objectModel
     * 
     * @var Model
     */
    public $objectModel;

    /**
     * AbstractMap::$objectTable
     *
     * @var string
     */
    public $objectTable;

    /**
     * AbstractMap::$objectPrimaryKey
     *
     * @var string
     */
    public $objectPrimaryKey;

    /**
     * AbstractMap::$objectPrimaryKeys
     *
     * @var array
     */
    public $objectPrimaryKeys = [];

    /**
     * AbstractMap::$objectForeignKey
     *
     * @var string
     */
    public $objectForeignKey;

    /**
     * AbstractMap::$associateModel
     *
     * @var Model
     */
    public $associateModel;

    /**
     * AbstractMap::$associateTable
     *
     * @var string
     */
    public $associateTable;

    /**
     * AbstractMap::$associatePrimaryKey
     *
     * @var string
     */
    public $associatePrimaryKey;

    /**
     * AbstractMap::$associatePrimaryKeys
     *
     * @var array
     */
    public $associatePrimaryKeys = [];

    /**
     * AbstractMap::$associateForeignKey
     *
     * @var string
     */
    public $associateForeignKey;

    // ------------------------------------------------------------------------

    /**
     * AbstractMap::getTableKey
     *
     * @param string      $table
     * @param string|null $prefix
     * @param string|null $suffix
     */
    protected function getTableKey($table, $prefix = null, $suffix = null)
    {
        $table = str_replace([
            't_',
            'tm_',
            'tr_',
            'tb_',
        ], '', $table);

        $keySegments = explode('_', $table);
        $keySegments = array_map('singular', $keySegments);

        if(isset($prefix)) {
            array_unshift($keySegments, $prefix);
        }

        if(isset($suffix)) {
            array_push($keySegments, $suffix);
        }

        return implode('_', $keySegments);
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractMap::mappingObjectModel
     *
     * @param string|Model $objectModel
     */
    protected function mappingObjectModel($objectModel)
    {
        if ($objectModel instanceof Model) {
            $this->objectModel = $objectModel;
            $this->objectTable = $this->objectModel->table;
            $this->objectPrimaryKey = $this->objectModel->primaryKey;
            $this->objectPrimaryKeys = $this->objectModel->primaryKeys;
        } elseif (class_exists($objectModel)) {
            $this->objectModel = models($objectModel);
            $this->objectTable = $this->objectModel->table;
            $this->objectPrimaryKey = $this->objectModel->primaryKey;
            $this->objectPrimaryKeys = $this->objectModel->primaryKeys;
        } else {
            $this->objectModel = new class extends Model {};
            $this->objectTable = $this->objectModel->table = $objectModel;
            $this->objectPrimaryKey = $this->objectModel->primaryKey = 'id';
            $this->objectPrimaryKeys = $this->objectModel->primaryKeys;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractMap::mappingAssociateModel
     *
     * @param string|Model $associateModel
     */
    protected function mappingAssociateModel($associateModel)
    {
        if ($associateModel instanceof Model) {
            $this->associateModel = $associateModel;
            $this->associateTable = $this->associateModel->table;
            $this->associatePrimaryKey = $this->associateModel->primaryKey;
            $this->associatePrimaryKeys = $this->associateModel->primaryKeys;
        } elseif (class_exists($associateModel)) {
            $this->associateModel = models($associateModel);
            $this->associateTable = $this->associateModel->table;
            $this->associatePrimaryKey = $this->associateModel->primaryKey;
            $this->associatePrimaryKeys = $this->associateModel->primaryKeys;
        } else {
            $this->associateModel = new class extends Model {};
            $this->associateTable = $this->associateModel->table = $associateModel;
            $this->associatePrimaryKey = $this->associateModel->primaryKey = 'id';
            $this->associatePrimaryKeys = $this->associateModel->primaryKeys;
        }

        if (empty($this->objectForeignKey)) {
            $this->objectForeignKey = $this->getTableKey($this->associateTable, $this->associatePrimaryKey);
        }

        if (empty($this->associateForeignKey)) {
            $this->associateForeignKey = $this->getTableKey($this->objectTable, $this->objectPrimaryKey);
        }
    }
}