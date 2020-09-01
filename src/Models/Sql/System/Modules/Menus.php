<?php


namespace O2System\Framework\Models\Sql\System\Modules;
// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;

/**
 * Class Menus
 * @package O2System\Framework\Models\Sql\System\Modules
 */
class Menus extends Model
{
    /**
     * Menus::$table
     *
     * @var string
     */
    public $table = 'sys_modules_menus';

    // ------------------------------------------------------------------------
    /**
     * Menus::$insertValidationRules
     *
     * @var array
     */
    public $insertValidationRules = [
        'id_sys_module' => 'optional|integer',
        'id_parent' => 'optional|integer',
        'position' => 'optional',
        'label' => 'optional',
        'description' => 'optional',
        'href' => 'optional',
        'attributes' => 'optional',
        'settings' => 'optional',
        'metadata' => 'optional',
    ];

    // ------------------------------------------------------------------------
    /**
     * Menus::$insertValidationCustomErrors
     *
     * @var array
     */
    public $insertValidationCustomErrors = [
        'id_sys_module' => [
            'integer' => 'System Module data must be an integer'
        ],
        'id_parent' => [
            'integer' => 'Parent data must be an integer'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Menus::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'id_sys_module' => 'optional|integer',
        'id_parent' => 'optional|integer',
        'position' => 'optional',
        'label' => 'optional',
        'description' => 'optional',
        'href' => 'optional',
        'attributes' => 'optional',
        'settings' => 'optional',
        'metadata' => 'optional',
    ];

    // ------------------------------------------------------------------------

    /**
     * Menus::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Menu id cannot be empty!',
            'integer' => 'Menu id data must be an integer'
        ],
        'id_sys_module' => [
            'integer' => 'System Module data must be an integer'
        ],
        'id_parent' => [
            'integer' => 'Parent data must be an integer'
        ],
    ];

}
