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

namespace O2System\Framework\Models\Sql\Traits;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\System\Logs;

/**
 * Trait LogTrait
 * @package O2System\Framework\Models\Sql\Traits
 */
trait LogTrait
{
    /**
     * Transactions::latestLog
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function latestLog()
    {
        $this->qb->orderBy($this->primaryKey, 'DESC');

        return $this->morphOne(Logs::class, 'ownership');
    }

    // ------------------------------------------------------------------------

    /**
     * Transactions::logs
     *
     * @return array|bool|\O2System\Database\DataObjects\Result
     */
    public function logs()
    {
        $this->qb->orderBy($this->primaryKey, 'DESC');

        return $this->morphToMany(Logs::class, 'ownership');
    }
}