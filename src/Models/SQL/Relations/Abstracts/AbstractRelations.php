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

namespace O2System\Framework\Models\SQL\Relations\Abstracts;

// ------------------------------------------------------------------------

use O2System\Framework\Models\DataObjects;
use O2System\Framework\Models\SQL\Relations;

/**
 * Class AbstractRelations
 *
 * @package O2System\Framework\Models\Abstracts
 */
abstract class AbstractRelations
{
    /**
     * Relations Mapper
     *
     * @var Relations\Mapper
     */
    protected $mapper;

    /**
     * Relations constructor.
     *
     * @param Relations\Mapper|Relations\Mappers\Inverse|Relations\Mappers\Inverse $mapper
     */
    public function __construct( $mapper )
    {
        $this->mapper = $mapper;
    }

    // ------------------------------------------------------------------------

    /**
     * Get Result
     *
     * @return DataObjects\Result\Row|array|bool
     */
    abstract public function getResult();
}