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
 * Class Mapper
 *
 * @package O2System\Framework\Models\Sql\Relations
 */
class Reference extends AbstractMap
{
    /**
     * Reference::__construct
     *
     * @param \O2System\Framework\Models\Sql\Model        $currentModel
     * @param string|\O2System\Framework\Models\Sql\Model $referenceModel
     * @param string|null                                 $foreignKey
     */
    public function __construct(
        Model $currentModel,
        $referenceModel,
        $foreignKey = null
    ) {
        // Mapping Models
        $this->mappingCurrentModel($currentModel);
        $this->mappingReferenceModel($referenceModel);

        // Defined Current Foreign Key
        $this->referenceForeignKey = (isset($foreignKey) ? $foreignKey
            : 'id_' . $this->currentTable);
    }
}