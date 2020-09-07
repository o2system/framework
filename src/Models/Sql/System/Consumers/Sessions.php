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
namespace O2System\Framework\Models\Sql\System\Consumers;
// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\System\Consumers;
// ------------------------------------------------------------------------
/**
 * Class Sessions
 * @package O2System\Framework\Models\Sql\System\Consumers
 */
class Sessions extends Model
{
    /**
     * Sessions::$table
     *
     * @var string
     */
    public $table = 'sys_consumers_sessions';

    // ------------------------------------------------------------------------
    /**
     * Sessions::$insertValidationRules
     *
     * @var array
     */
    public $insertValidationRules = [
        'id_sys_consumer' => 'required|integer',
        'token' => 'required',
        'payload' => 'required',
        'timestamp' => 'required|date[Y-m-d h:i:s]',
        'expires' => 'optional'
    ];

    // ------------------------------------------------------------------------
    /**
     * Sessions::$insertValidationCustomErrors
     *
     * @var array
     */
    public $insertValidationCustomErrors = [
        'id_sys_consumer' => [
            'required' => 'Session id_sys_consumer cannot be empty!',
            'integer' => 'Session id_sys_consumer data must be an integer'
        ],
        'token' => [
            'required' => 'Session endpoint cannot be empty!'
        ],
        'payload' => [
            'required' => 'Session permission cannot be empty!'
        ],
        'timestamp' => [
            'required' => 'Session permission cannot be empty!',
            'date' => 'Session date format must be Y-m-d H:i:s'
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
        'id_sys_consumer' => 'required|integer',
        'token' => 'required',
        'payload' => 'required',
        'timestamp' => 'required|date[Y-m-d h:i:s]',
        'expires' => 'optional'
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
        'id_sys_consumer' => [
            'required' => 'Session id_sys_consumer cannot be empty!',
            'integer' => 'Session id_sys_consumer data must be an integer'
        ],
        'token' => [
            'required' => 'Session endpoint cannot be empty!'
        ],
        'payload' => [
            'required' => 'Session permission cannot be empty!'
        ],
        'timestamp' => [
            'required' => 'Session permission cannot be empty!',
            'date' => 'Session date format must be Y-m-d H:i:s'
        ],
    ];

    // ------------------------------------------------------------------------
    /**
     * Sessions::consumers
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function consumers()
    {
        return $this->belongsTo(Consumers::class, 'id_sys_consumer');
    }

}
