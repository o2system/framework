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
 * Class Authorities
 * @package O2System\Framework\Models\Sql\System\Consumers
 */
class Authorities extends Model
{
    /**
     * @var string
     */
    public $table = 'sys_consumers_authorities';

    // ------------------------------------------------------------------------
    /**
     * Authorities::$insertValidationRules
     *
     * @var array
     */
    public $insertValidationRules = [
        'id_sys_consumer' => 'required|integer',
        'endpoint' => 'required',
        'permission' => 'required',
        'scope' => 'optional',
    ];

    // ------------------------------------------------------------------------
    /**
     * Authorities::$insertValidationCustomErrors
     *
     * @var array
     */
    public $insertValidationCustomErrors = [
        'id_sys_consumer' => [
            'required' => 'Authority id sys consumer cannot be empty!',
            'integer' => 'Authority id sys consumer data must be an integer'
        ],
        'endpoint' => [
            'required' => 'Authority endpoint cannot be empty!',
        ],
        'permission' => [
            'required' => 'Authority permission cannot be empty!'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Authorities::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'id_sys_consumer' => 'required|integer',
        'endpoint' => 'required',
        'permission' => 'required',
        'scope' => 'optional',
    ];

    // ------------------------------------------------------------------------

    /**
     * Authorities::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Authority id cannot be empty!',
            'integer' => 'Authority id data must be an integer'
        ],
        'id_sys_consumer' => [
            'required' => 'Authority id sys consumer cannot be empty!',
            'integer' => 'Authority id sys consumer data must be an integer'
        ],
        'endpoint' => [
            'required' => 'Authority endpoint cannot be empty!',
        ],
        'permission' => [
            'required' => 'Authority permission cannot be empty!'
        ],
    ];

    // ------------------------------------------------------------------------
    /**
     * Authorities::consumers
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function consumers()
    {
        return $this->belongsTo(Consumers::class, 'id_sys_consumer');
    }

}
