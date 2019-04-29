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

namespace O2System\Framework\Models\Sql\Relations\Maps;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Relations\Maps\Abstracts\AbstractMap;

/**
 * Class Intermediary
 *
 * @package O2System\Framework\Models\Sql\Intermediarys\Maps
 */
class Intermediary extends AbstractMap
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
     * Intermediary::$intermediaryCurrentForeignKey
     *
     * @var string
     */
    public $intermediaryCurrentForeignKey;

    /**
     * Intermediary::$intermediaryReferenceForeignKey
     *
     * @var string
     */
    public $intermediaryReferenceForeignKey;

    // ------------------------------------------------------------------------

    /**
     * Intermediary::__construct
     *
     * @param \O2System\Framework\Models\Sql\Model        $currentModel
     * @param string|\O2System\Framework\Models\Sql\Model $referenceModel
     * @param string|\O2System\Framework\Models\Sql\Model $intermediaryModel
     * @param string|null                                 $intermediaryCurrentForeignKey
     * @param string|null                                 $intermediaryReferenceForeignKey
     */
    public function __construct(
        Model $currentModel,
        $referenceModel,
        $intermediaryModel,
        $intermediaryCurrentForeignKey = null,
        $intermediaryReferenceForeignKey = null
    ) {
        // Mapping  Models
        $this->mappingCurrentModel($currentModel);
        $this->mappingReferenceModel($referenceModel);
        $this->mappingIntermediaryModel($intermediaryModel);

        $this->intermediaryCurrentForeignKey = empty($intermediaryCurrentForeignKey)
            ? 'id_' . $this->currentTable
            : $intermediaryCurrentForeignKey;

        $this->intermediaryReferenceForeignKey = empty($intermediaryReferenceForeignKey)
            ? 'id_' . $this->referenceTable
            : $intermediaryReferenceForeignKey;
    }

    // ------------------------------------------------------------------------

    /**
     * Intermediary::mappingIntermediaryModel
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
            $this->intermediaryModel = new class extends Model
            {
            };
            $this->intermediaryModel->table = $this->referenceTable = $intermediaryModel;
        }
    }

    // ------------------------------------------------------------------------
}