<?php


namespace O2System\Framework\Models\Sql\System\Modules;


use O2System\Framework\Models\Sql\Model;

/**
 * Class Languages
 * @package O2System\Framework\Models\Sql\System\Modules
 */
class Languages extends Model
{
    /**
     * @var string
     */
    public $table = 'sys_modules_languages';

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
        'ideom' => [
            'required' => 'System Module Language ideom cannot be empty!'
        ],
        'key' => [
            'required' => 'System Module Language key cannot be empty!'
        ],
        'translation' => [
            'required' => 'System Module Language translation cannot be empty!'
        ],

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
        'ideom' => [
            'required' => 'System Module Language ideom cannot be empty!'
        ],
        'key' => [
            'required' => 'System Module Language key cannot be empty!'
        ],
        'translation' => [
            'required' => 'System Module Language translation cannot be empty!'
        ],
    ];

}
