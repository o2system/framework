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

namespace O2System\Framework\Models\Sql\Relations\Abstracts;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\DataObjects;
use O2System\Framework\Models\Sql\Relations;

/**
 * Class AbstractRelations
 *
 * @package O2System\Framework\Models\Abstracts
 */
abstract class AbstractRelation
{
    /**
     * Relations Map
     *
     * @var Relations\Maps\Reference|Relations\Maps\Inverse|Relations\Maps\Intermediary|\O2System\Framework\Models\Sql\Relations\Maps\Through
     */
    protected $map;

    /**
     * Relations::__construct
     *
     * @param Relations\Maps\Reference|Relations\Maps\Inverse|Relations\Maps\Intermediary|\O2System\Framework\Models\Sql\Relations\Maps\Through $map
     */
    public function __construct($map)
    {
        $this->map = $map;
    }

    // ------------------------------------------------------------------------

    /**
     * Get Result
     *
     * @return DataObjects\Result\Row|array|bool
     */
    abstract public function getResult();
}