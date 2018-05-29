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

namespace O2System\Framework\Models\Sql\Relations\Maps;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;

/**
 * Class Intermediate
 *
 * @package O2System\Framework\Models\Sql\Relations\Maps
 */
class Intermediary
{
    /**
     * Relation Model
     *
     * @var Model
     */
    public $relationModel;

    /**
     * Relation Table
     *
     * @var string
     */
    public $relationTable;

    /**
     * Relation Primary Key
     *
     * @var string
     */
    public $relationPrimaryKey;

    /**
     * Reference Table
     *
     * @var string
     */
    public $referenceTable;

    /**
     * Reference Primary Key
     *
     * @var string
     */
    public $referencePrimaryKey;

    /**
     * Pivot Table
     *
     * @var string
     */
    public $pivotTable;

    /**
     * Pivot Relation Key
     *
     * @var string
     */
    public $pivotRelationKey;

    /**
     * Pivot Reference Key
     *
     * @var string
     */
    public $pivotReferenceKey;

    // ------------------------------------------------------------------------

    /**
     * IntermediateMapper constructor.
     *
     * @param Model        $relationModel
     * @param string|Model $referenceModel
     * @param string|Model $intermediateModel
     * @param string|null  $pivotTable
     * @param string|null  $relationForeignKey
     * @param string|null  $referencePrimaryKey
     */
    public function __construct(
        Model $relationModel,
        $referenceModel,
        $pivotTable = null,
        $relationForeignKey = null,
        $referencePrimaryKey = null
    ) {
        // Map Relation Model
        $this->mapRelationModel($relationModel);

        // Map Reference Model
        $this->mapReferenceModel($referenceModel);

        // Map Intermediate Table
        $this->pivotTable = $this->relationTable . '_' . $this->referenceTable;

        if (isset($pivotTable)) {
            $this->pivotTable = $pivotTable;
        }

        $this->pivotRelationKey = $this->pivotTable . '.' . $this->pivotRelationKey;
        $this->pivotReferenceKey = $this->pivotTable . '.' . $this->pivotReferenceKey;
    }

    // ------------------------------------------------------------------------

    /**
     * Map Relation Model
     *
     * @param string|Model $relationModel
     *
     * @return void
     */
    private function mapRelationModel($relationModel)
    {
        if ($relationModel instanceof Model) {
            $this->relationModel = $relationModel;
            $this->relationTable = $this->relationModel->table;
            $this->relationPrimaryKey = $this->relationModel->primaryKey;
            $this->pivotRelationKey = $this->relationModel->primaryKey . '_' . $this->relationModel->table;
        } elseif (class_exists($relationModel)) {
            $this->relationModel = new $relationModel();
            $this->relationTable = $this->relationModel->table;
            $this->relationPrimaryKey = $this->relationModel->table . '.' . $this->relationModel->primaryKey;
            $this->pivotRelationKey = $this->relationModel->primaryKey . '_' . $this->relationModel->table;
        } else {
            $this->relationTable = $relationModel;
            $this->relationPrimaryKey = $this->relationTable . '.id';
            $this->pivotRelationKey = $this->relationTable . '.id_' . $this->relationTable;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Map Relation Model
     *
     * @param string|Model $referenceModel
     *
     * @return void
     */
    private function mapReferenceModel($referenceModel)
    {
        if ($referenceModel instanceof Model) {
            $this->referenceTable = $referenceModel->table;
            $this->referencePrimaryKey = $referenceModel->primaryKey;
            $this->pivotReferenceKey = $referenceModel->primaryKey . '_' . $this->referenceTable;
        } elseif (class_exists($referenceModel)) {
            $referenceModel = new $referenceModel();
            $this->referenceTable = $referenceModel->table;
            $this->referencePrimaryKey = $this->referenceTable . '.' . $referenceModel->primaryKey;
            $this->pivotReferenceKey = $referenceModel->primaryKey . '_' . $this->referenceTable;
        } else {
            $this->referenceTable = $referenceModel;
            $this->referencePrimaryKey = $this->referenceTable . '.id';
            $this->pivotReferenceKey = 'id_' . $this->referenceTable;
        }
    }
}