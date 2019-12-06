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
use O2System\Framework\Models\Sql\Relations\Maps\Traits\IntermediaryTrait;

/**
 * Class Associate
 * @package O2System\Framework\Models\Sql\Relations\Maps
 */
class Associate extends AbstractMap
{
    use IntermediaryTrait;
    
    /**
     * Associate::__construct
     *
     * @param \O2System\Framework\Models\Sql\Model        $objectModel
     * @param string|\O2System\Framework\Models\Sql\Model $associateModel
     * @param string|null                               $associateForeignKey
     */
    public function __construct(
        Model $objectModel,
        $associateModel,
        $associateForeignKey = null
    ) {
        // Mapping Models
        $this->mappingObjectModel($objectModel);
        $this->mappingAssociateModel($associateModel);

        // Defined Associate Foreign Key
        $this->associateForeignKey = empty($associateForeignKey)
            ? $this->getTableKey($this->objectTable, $this->objectPrimaryKey)
            : $associateForeignKey;
    }
}