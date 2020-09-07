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

namespace O2System\Framework\Models\Sql\System;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\System\Consumers\Authorities;

/**
 * Class Consumers
 * @package O2System\Framework\Models\Sql\System
 */
class Consumers extends Model
{
    /**
     * Consumers::$table
     *
     * @var string
     */
    public $table = 'sys_consumers';

    // ------------------------------------------------------------------------
    /**
     * Consumers::$insertValidationRules
     *
     * @var array
     */
    public $insertValidationRules = [
        'namespace' => 'required',
        'key' => 'required',
        'secret' => 'required',
        'callback' => 'optional',
    ];

    // ------------------------------------------------------------------------
    /**
     * Consumers::$insertValidationCustomErrors
     *
     * @var array
     */
    public $insertValidationCustomErrors = [
        'namespace' => [
            'required' => 'Consumer namespace  cannot be empty!'
        ],
        'key' => [
            'required' => 'Consumer key cannot be empty!',
        ],
        'secret' => [
            'required' => 'Consumer model secret cannot be empty!'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Consumers::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'timestamp' => 'optional|date[Y-m-d h:i:s]',
        'status' => 'optional',
        'message' => 'optional',
        'log_id' => 'required|integer',
        'log_model' => 'required'
    ];

    // ------------------------------------------------------------------------

    /**
     * Consumers::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Consumer id cannot be empty!',
            'integer' => 'Consumer id data must be an integer'
        ],
        'namespace' => [
            'required' => 'Consumer namespace  cannot be empty!'
        ],
        'key' => [
            'required' => 'Consumer key cannot be empty!',
        ],
        'secret' => [
            'required' => 'Consumer model secret cannot be empty!'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Consumers::sessions
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function sessions()
    {
        return $this->hasMany(Sessions::class);
    }

    // ------------------------------------------------------------------------
    /**
     * Consumers::authorities
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function authorities()
    {
        return $this->hasMany(Authorities::class);

    }

}
