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

namespace O2System\Framework\Models\Sql\Relations\Maps\Traits;

// ------------------------------------------------------------------------
use O2System\Framework\Models\Sql\Model;

/**
 * Trait IntermediaryTrait
 * @package O2System\Framework\Models\Sql\Relations\Maps\Traits
 */
trait IntermediaryTrait
{
    /**
     * Intermediary::$intermediaryModel
     *
     * @var \O2System\Framework\Models\Sql\Model
     */
    public $intermediaryModel;

    /**
     * Intermediary::$intermediaryTable
     *
     * @var string
     */
    public $intermediaryTable;

    /**
     * Intermediary::$intermediaryPrimaryKey
     *
     * @var string
     */
    public $intermediaryPrimaryKey;

    /**
     * Intermediary::$intermediaryForeignKey
     *
     * @var string
     */
    public $intermediaryForeignKey;

    /**
     * Intermediary::$intermediaryassociateForeignKey
     *
     * @var string
     */
    public $intermediaryAssociateForeignKey;

    // ------------------------------------------------------------------------

    /**
     * IntermediaryTrait::setIntermediary
     *
     * @param string|Model  $intermediaryModel
     * @param string|null   $intermediaryForeignKey
     * @param string|null   $intermediaryAssociateForeignKey
     */
    public function setIntermediary(
        $intermediaryModel,
        $intermediaryForeignKey = null,
        $intermediaryAssociateForeignKey = null
    )
    {
        $this->mappingIntermediaryModel($intermediaryModel);
        
        $this->intermediaryForeignKey = empty($intermediaryForeignKey)
            ? $this->getTableKey($this->objectTable, $this->objectPrimaryKey)
            : $intermediaryForeignKey;

        $this->intermediaryAssociateForeignKey = empty($intermediaryAssociateForeignKey)
            ? $this->getTableKey($this->associateTable, $this->associatePrimaryKey)
            : $intermediaryAssociateForeignKey;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * IntermediaryTrait::mappingIntermediaryModel
     *
     * @param string|\O2System\Framework\Models\Sql\Model $intermediaryModel
     */
    protected function mappingIntermediaryModel($intermediaryModel)
    {
        if ($intermediaryModel instanceof Model) {
            $this->intermediaryModel = $intermediaryModel;
            $this->intermediaryTable = $intermediaryModel->table;
            $this->intermediaryPrimaryKey = $this->intermediaryModel->primaryKey;
        } elseif (class_exists($intermediaryModel)) {
            $this->intermediaryModel = models($intermediaryModel);
            $this->intermediaryTable = $this->intermediaryModel->table;
            $this->intermediaryPrimaryKey = $this->intermediaryModel->primaryKey;
        } else {
            $this->intermediaryModel = new class extends Model{};
            $this->intermediaryModel->table = $this->intermediaryTable  = $intermediaryModel;
            $this->intermediaryPrimaryKey = $this->intermediaryModel->primaryKey = 'id';
        }
    }
}