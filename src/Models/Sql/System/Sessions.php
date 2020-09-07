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

/**
 * Class Sessions
 * @package O2System\Framework\Models\Sql\System
 */
class Sessions extends Model
{
    /**
     * Sessions::$table
     *
     * @var string
     */
    public $table = 'sys_sessions';
    // ------------------------------------------------------------------------

    /**
     * Sessions::$insertValidationRules
     *
     * @var array
     */
    public $insertValidationRules = [
        'id_session' => 'required',
        'ssid' => 'required',
        'jwt' => 'required',
        'expires' => 'optional',
        'useragent' => 'optional',
        'ownership_id' => 'required',
        'ownership_model' => 'required',
    ];

    // ------------------------------------------------------------------------
    /**
     * Sessions::$insertValidationCustomErrors
     *
     * @var array
     */
    public $insertValidationCustomErrors = [
        'id_session' => [
            'required' => 'Session ID cannot be empty!'
        ],
        'ssid' => [
            'required' => 'SSID name cannot be empty!'
        ],
        'jwt' => [
            'required' => 'JWT name cannot be empty!'
        ],
        'ownership_id' => [
            'required' => 'Ownership id cannot be empty!',
            'integer' => 'Ownership id data must be an integer'
        ],
        'ownership_model' => [
            'required' => 'Ownership model cannot be empty!'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Sessions::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
    ];

    // ------------------------------------------------------------------------

    /**
     * Sessions::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Session id cannot be empty!',
            'integer' => 'Session id data must be an integer'
        ],
        'id_session' => [
            'required' => 'Session ID cannot be empty!'
        ],
        'ssid' => [
            'required' => 'SSID name cannot be empty!'
        ],
        'jwt' => [
            'required' => 'JWT name cannot be empty!'
        ],
        'ownership_id' => [
            'required' => 'Ownership id cannot be empty!',
            'integer' => 'Ownership id data must be an integer'
        ],
        'ownership_model' => [
            'required' => 'Ownership model cannot be empty!'
        ],
    ];
    // ------------------------------------------------------------------------

    /**
     * Sessions::ownership
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function ownership()
    {
        return $this->morphTo();
    }
}
