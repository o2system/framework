<?php


namespace O2System\Framework\Models\Sql\System;


use O2System\Framework\Models\Sql\Model;

class Tags extends Model
{
    public $table = 'sys_tags';

    // ------------------------------------------------------------------------
    /**
     * Tags::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [
        'ownership_id' => 'required|integer',
        'ownership_model' => 'required',
        'tag_id' => 'required|integer',
        'tag_model' => 'required',
        'tag_role' => 'optional'
    ];

    // ------------------------------------------------------------------------
    /**
     * Tags::$createValidationCustomErrors
     *
     * @var array
     */
    public $createValidationCustomErrors = [
        'ownership_id' => [
            'required' => 'Tag id cannot be empty!',
            'integer' => 'Tag id data must be an integer'
        ],
        'ownership_model' => [
            'required' => 'Ownership model id cannot be empty!'
        ],
        'tag_id' => [
            'required' => 'Tag id cannot be empty!',
            'integer' => 'Tag id data must be an integer'
        ],
        'tag_model' => [
            'required' => 'Tag model id cannot be empty!'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Tags::$updateValidationRules
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
     * Tags::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Tag id cannot be empty!',
            'integer' => 'Tag id data must be an integer'
        ],
        'ownership_id' => [
            'required' => 'Tag id cannot be empty!',
            'integer' => 'Tag id data must be an integer'
        ],
        'ownership_model' => [
            'required' => 'Ownership model id cannot be empty!'
        ],
        'tag_id' => [
            'required' => 'Tag id cannot be empty!',
            'integer' => 'Tag id data must be an integer'
        ],
        'tag_model' => [
            'required' => 'Tag model id cannot be empty!'
        ],
    ];

}
