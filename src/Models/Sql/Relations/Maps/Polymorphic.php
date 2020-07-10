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
 * Class Polymorphic
 * @package O2System\Framework\Models\Sql\Relations\Maps
 */
class Polymorphic extends AbstractMap
{
    use IntermediaryTrait;

    /**
     * Polymorphic::$morphKey
     *
     * @var string
     */
    public $morphKey;

    // ------------------------------------------------------------------------

    /**
     * Polymorphic::__construct
     *
     * @param \O2System\Framework\Models\Sql\Model        $objectModel
     * @param string|\O2System\Framework\Models\Sql\Model $associativeModel
     * @param string                                      $morphKey
     */
    public function __construct(
        Model $objectModel,
        $associativeModel,
        $morphKey
    ) {
        // Mapping Models
        $this->mappingObjectModel($objectModel);
        $this->mappingAssociateModel($associativeModel);

        $this->morphKey = $morphKey;
    }

    // ------------------------------------------------------------------------

    /**
     * Polymorphic::setKey
     * 
     * @param string $key
     *
     * @return static
     */
    public function setKey(string $key)
    {
        $this->morphKey = $key;
        
        return $this;
    }
}