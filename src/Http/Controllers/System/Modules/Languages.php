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
namespace O2System\Framework\Http\Controllers\System\Modules;

// ------------------------------------------------------------------------

use O2System\Framework\Http\Controllers\Restful;

/**
 * Class Languages
 * @package O2System\Framework\Http\Controllers\System\Modules
 */
class Languages extends Restful
{
    /**
     * Languages::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Modules\Languages';

    // ------------------------------------------------------------------------
    /**
     * Languages::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [
        'id_sys_module' => 'optional|integer',
        'ideom' => 'required',
        'key' => 'required',
        'translation' => 'required',
    ];

    // ------------------------------------------------------------------------
    /**
     * Languages::$createValidationCustomErrors
     *
     * @var array
     */
    public $createValidationCustomErrors = [
        'id_sys_module' => [
            'required' => 'System Module cannot be empty!',
            'integer' => 'System Module data must be an integer'
        ],
        'ideom' => ['required' => 'System Module Language ideom cannot be empty!'],
        'key' => ['required' => 'System Module Language key cannot be empty!'],
        'translation' => ['required' => 'System Module Language translation cannot be empty!'],

    ];

    // ------------------------------------------------------------------------

    /**
     * Languages::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'id_sys_module' => 'optional|integer',
        'ideom' => 'required',
        'key' => 'required',
        'translation' => 'required',
    ];

    // ------------------------------------------------------------------------

    /**
     * Languages::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Setting id cannot be empty!',
            'integer' => 'Setting id data must be an integer'
        ],
        'id_sys_module' => [
            'required' => 'System Module cannot be empty!',
            'integer' => 'System Module data must be an integer'
        ],
        'ideom' => ['required' => 'System Module Language ideom cannot be empty!'],
        'key' => ['required' => 'System Module Language key cannot be empty!'],
        'translation' => ['required' => 'System Module Language translation cannot be empty!'],
    ];

    // ------------------------------------------------------------------------

    /**
     * Languages::$getValidationRules
     *
     * @var array
     */
    public $getValidationRules = [
        'id_sys_module' => 'optional|integer'
    ];

    // ------------------------------------------------------------------------

    /**
     * Languages::$getValidationCustomErrors
     *
     * @var array
     */
    public $getValidationCustomErrors = [
        'id_sys_module' => [
            'integer' => 'System Module data must be an integer'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Languages::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
        'key',
        'translation',
    ];
}