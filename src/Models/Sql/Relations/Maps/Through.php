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
 * Class Through
 *
 * @package O2System\Framework\Models\Sql\Relations\Maps
 */
class Through
{
    /**
     * @var Model
     */
    public $pivotModel;

    /**
     * @var string
     */
    public $pivotTable;

    /**
     * Reference Foreign Key
     *
     * @var string
     */
    public $pivotForeignKey;

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
     * Relation Foreign Key
     *
     * @var string
     */
    public $relationForeignKey;

    /**
     * Reference Model
     *
     * @var Model
     */
    public $referenceModel;

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

    // ------------------------------------------------------------------------

    /**
     * IntermediateMapper constructor.
     *
     * @param Model        $pivotModel
     * @param string|Model $relationModel
     * @param string|Model $intermediateModel
     * @param string|null  $referenceModel
     * @param string|null  $relationForeignKey
     * @param string|null  $pivotForeignKey
     */
    public function __construct(
        Model $pivotModel,
        $pivotForeignKey = null,
        $relationModel,
        $relationForeignKey = null,
        $referenceModel = null
    ) {
        $this->pivotModel =& $pivotModel;
        $this->pivotTable = $pivotModel->table;
        $this->pivotForeignKey = $pivotForeignKey;

        // Map Relation Model
        $this->mapRelationModel($relationModel);
        $this->relationForeignKey = $this->relationTable . '.' . $relationForeignKey;

        // Map Reference Model
        $this->mapReferenceModel($referenceModel);
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
        } elseif (class_exists($relationModel)) {
            $this->relationModel = new $relationModel();
            $this->relationTable = $this->relationModel->table;
        } else {
            $this->relationTable = $relationModel;
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
            $this->referenceModel = $referenceModel;
            $this->referenceTable = $referenceModel->table;
            $this->referencePrimaryKey = $this->referenceModel->table . '.' . $this->referenceModel->primaryKey;
        } elseif (class_exists($referenceModel)) {
            $this->referenceModel = new $referenceModel();
            $this->referenceTable = $this->referenceModel->table;
            $this->referencePrimaryKey = $this->referenceModel->table . '.' . $this->referenceModel->primaryKey;
        } else {
            $this->referenceTable = $referenceModel;
            $this->referencePrimaryKey = $this->referenceModel->table . '.' . 'id';
        }
    }
}