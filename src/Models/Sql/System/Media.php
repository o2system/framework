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
use O2System\Framework\Models\Sql\Traits\MetadataTrait;
use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class Media
 * @package O2System\Framework\Models\Sql\System
 */
class Media extends Model
{
    use MetadataTrait;

    /**
     * Media::$table
     *
     * @var string
     */
    public $table = 'sys_media';
    // ------------------------------------------------------------------------

    /**
     * Media::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [];

    // ------------------------------------------------------------------------
    /**
     * Media::$createValidationRules
     *
     * @var array
     */
    public $createValidationRules = [
        'label' => 'required',
        'filename' => 'required',
        'filepath' => 'required',
        'mime' => 'required',
    ];

    // ------------------------------------------------------------------------
    /**
     * Media::$createValidationCustomErrors
     *
     * @var array
     */
    public $createValidationCustomErrors = [
        'label' => [
            'required' => 'Label media cannot be empty!'
        ],
        'filename' => [
            'required' => 'Filename media cannot be empty!'
        ],
        'filepath' => [
            'required' => 'Filepath media cannot be empty!'
        ],
        'mime' => [
            'required' => 'Mime media cannot be empty!'
        ]
    ];

    // ------------------------------------------------------------------------

    /**
     * Media::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'label' => 'required',
        'filename' => 'required',
        'filepath' => 'required',
        'mime' => 'required',
        'ownership_id' => 'required|integer',
        'ownership_model' => 'optional'
    ];

    // ------------------------------------------------------------------------

    /**
     * Media::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Log id cannot be empty!',
            'integer' => 'Log id data must be an integer'
        ],
        'label' => [
            'required' => 'Label media cannot be empty!'
        ],
        'filename' => [
            'required' => 'Filename media cannot be empty!'
        ],
        'filepath' => [
            'required' => 'Filepath media cannot be empty!'
        ],
        'mime' => [
            'required' => 'Mime media cannot be empty!'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Media::rebuildRow
     *
     * @param $row
     * @throws \Exception
     */
    public function rebuildRow(&$row)
    {
        $filePath = PATH_STORAGE . str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $row->filepath);

        if (!is_file($filePath)) {
            if ($row->record->type === 'IMAGE') {
                $filePath = PATH_STORAGE . 'images/default/image-not-found.jpg';
            }
        }

        $row->record->upload = new SplArrayObject([
            'user' => $row->record->create->user,
            'timestamp' => $row->record->create->timestamp
        ]);

        $row->url = $row->record->type === 'IMAGE' ? images_url($filePath) : storage_url($filePath);
    }

    // ------------------------------------------------------------------------
    /**
     * Media::image
     *
     * @return string
     */
    public function image(): string
    {
        $image = PATH_STORAGE . 'images/' .$this->row->filepath;
        if (is_file($image)) {
            return images_url($image);
        }
        return images_url('/images/default/no-image.jpg');
    }
}
