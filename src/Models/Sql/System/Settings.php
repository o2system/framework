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
use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class Settings
 * @package O2System\Framework\Models\Sql\System
 */
class Settings extends Model
{
    /**
     * Settings::$table
     *
     * @var string
     */
    public $table = 'sys_settings';

    // ------------------------------------------------------------------------
    /**
     * Settings::$insertValidationRules
     *
     * @var array
     */
    public $insertValidationRules = [
        'ownership_id' => 'required',
        'ownership_model' => 'required',
        'key' => 'required',
        'value' => 'optional'
    ];

    // ------------------------------------------------------------------------
    /**
     * Settings::$insertValidationCustomErrors
     *
     * @var array
     */
    public $insertValidationCustomErrors = [
        'ownership_id' => [
            'required' => 'Ownership id cannot be empty!',
        ],
        'ownership_model' => [
            'required' => 'Ownership model cannot be empty!'
        ],
        'key' => [
            'required' => 'Setting name cannot be empty!'
        ]
    ];

    // ------------------------------------------------------------------------

    /**
     * Settings::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'ownership_id' => 'required',
        'ownership_model' => 'required',
        'key' => 'required',
        'value' => 'optional'
    ];

    // ------------------------------------------------------------------------

    /**
     * Settings::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Settings id cannot be empty!',
            'integer' => 'Settings id data must be an integer'
        ],
        'ownership_id' => [
            'required' => 'Ownership id cannot be empty!',
            'integer' => 'Ownership id data must be an integer'
        ],
        'ownership_model' => [
            'required' => 'Ownership model cannot be empty!'
        ],
        'key' => [
            'required' => 'Setting key cannot be empty!'
        ]
    ];

    // ------------------------------------------------------------------------

    /**
     * Settings::ownership
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function ownership()
    {
        return $this->morphTo();
    }

}
