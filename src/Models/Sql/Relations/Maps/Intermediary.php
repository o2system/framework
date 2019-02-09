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
    public $intermediaryModel;
    public $intermediaryTable;
    public $intermediaryCurrentForeignKey;
    public $intermediaryReferenceForeignKey;

    // ------------------------------------------------------------------------

    public function __construct(
        Model $currentModel,
        $referenceModel,
        $intermediaryModel = null,
        $intermediaryCurrentForeignKey = null,
        $intermediaryReferenceForeignKey = null
    ) {

        $this->currentModel =& $currentModel;
        $this->currentTable = $currentModel->table;
        $this->currentPrimaryKey = $currentModel->primaryKey;

        // Mapping Reference Model
        $this->mappingReferenceModel($referenceModel);

        // Defined Current Foreign Key
        $this->currentForeignKey = (isset($foreignKey) ? $foreignKey
            : $this->currentForeignKey);

        $this->mappingIntermediaryModel($intermediaryModel);

        $this->intermediaryCurrentForeignKey = empty($intermediaryCurrentForeignKey)
            ? 'id_' . $this->currentTable
            : $intermediaryCurrentForeignKey;

        $this->intermediaryReferenceForeignKey = empty($intermediaryReferenceForeignKey)
            ? 'id_' . $this->referenceTable
            : $intermediaryReferenceForeignKey;
    }

    // ------------------------------------------------------------------------

    protected function mappingIntermediaryModel($intermediaryModel)
    {
        if ($intermediaryModel instanceof Model) {
            $this->intermediaryModel = $intermediaryModel;
            $this->intermediaryTable = $intermediaryModel->table;
        } elseif (class_exists($intermediaryModel)) {
            $this->intermediaryModel = new $intermediaryModel();
            $this->intermediaryTable = $intermediaryModel->table;
        } else {
            $this->intermediaryModel = new class extends Model
            {
            };
            $this->intermediaryModel->table = $this->referenceTable = $intermediaryModel;
        }
    }

    // ------------------------------------------------------------------------
}