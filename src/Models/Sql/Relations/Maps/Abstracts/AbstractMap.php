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
     * AbstractMap::$currentModel
     * 
     * @var Model
     */
    public $currentModel;

    /**
     * AbstractMap::$currentTable
     *
     * @var string
     */
    public $currentTable;

    /**
     * AbstractMap::$currentPrimaryKey
     *
     * @var string
     */
    public $currentPrimaryKey;

    /**
     * AbstractMap::$currentForeignKey
     *
     * @var string
     */
    public $currentForeignKey;

    /**
     * AbstractMap::$referenceModel
     *
     * @var Model
     */
    public $referenceModel;

    /**
     * AbstractMap::$referenceTable
     *
     * @var string
     */
    public $referenceTable;

    /**
     * AbstractMap::$referencePrimaryKey
     *
     * @var string
     */
    public $referencePrimaryKey;

    /**
     * AbstractMap::$referenceForeignKey
     *
     * @var string
     */
    public $referenceForeignKey;

    // ------------------------------------------------------------------------

    /**
     * AbstractMap::mappingCurrentModel
     *
     * @param string|Model $currentModel
     */
    protected function mappingCurrentModel($currentModel)
    {
        if ($currentModel instanceof Model) {
            $this->currentModel = $currentModel;
            $this->currentTable = $this->currentModel->table;
            $this->currentPrimaryKey = $this->currentModel->primaryKey;
        } elseif (class_exists($currentModel)) {
            $this->currentModel = models($currentModel);
            $this->currentTable = $this->currentModel->table;
            $this->currentPrimaryKey = $this->currentModel->primaryKey;
        } else {
            $this->currentTable = $currentModel;
            $this->currentPrimaryKey = 'id';
        }
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractMap::mappingReferenceModel
     *
     * @param string|Model $referenceModel
     */
    protected function mappingReferenceModel($referenceModel)
    {
        if ($referenceModel instanceof Model) {
            $this->referenceModel = $referenceModel;
            $this->referenceTable = $this->referenceModel->table;
            $this->referencePrimaryKey = $this->referenceModel->primaryKey;
        } elseif (class_exists($referenceModel)) {
            $this->referenceModel = models($referenceModel);
            $this->referenceTable = $this->referenceModel->table;
            $this->referencePrimaryKey = $this->referenceModel->primaryKey;
        } else {
            $this->referenceModel = new class extends Model
            {
            };
            $this->referenceModel->table = $this->referenceTable = $referenceModel;
            $this->referenceModel->primaryKey = $this->referencePrimaryKey = 'id';
        }

        if (empty($this->currentForeignKey)) {
            $this->currentForeignKey = $this->referencePrimaryKey . '_' . str_replace([
                    't_',
                    'tm_',
                    'tr_',
                    'tb_',
                ], '', $this->referenceTable);
        }
    }
}