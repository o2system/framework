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
use O2System\Framework\Models\Sql\Traits\HierarchicalTrait;
use O2System\Framework\Models\Sql\Traits\MetadataTrait;

/**
 * Class Storage
 * @package O2System\Framework\Models\Sql\System
 */
class Storage extends Model
{
    use HierarchicalTrait;

    /**
     * Storage::$table
     *
     * @var string
     */
    public $table = 'sys_storage';

    /**
     * Storage::$uploadFilePaths
     *
     * @var array
     */
    public $uploadFilePaths = [
        'filepath' => PATH_STORAGE
    ];

    /**
     * Storage::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        'fileurl'
    ];

    // ------------------------------------------------------------------------

    /**
     * Storage::ownership
     *
     * @return array|bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function ownership()
    {
        return $this->morphTo();
    }

    // ------------------------------------------------------------------------

    public function fileurl()
    {
        if(is_file($filePath = $this->uploadFilePaths['filepath'] . $this->row->filepath)) {
            return storage_url($filePath);
        } else {
            return storage_url('images/not-found.jpg');
        }
    }
    // ------------------------------------------------------------------------
    /**
     * Documents::getOrganizationFolders
     *
     * @return bool|\O2System\Database\DataObjects\Result
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getOrganizationFolders()
    {
        if($result = models(Organizations::class)->qb->from('spaces_organizations')->where([
            'id_space' => session()->space->id
        ])
            ->whereIn('record_depth', [0,1])->orderBy('name')->get()) {
            loader()->helper('Number');
            $rootPath = PATH_STORAGE . 'spaces' . DIRECTORY_SEPARATOR . session()->company->name . DIRECTORY_SEPARATOR;

            foreach($result as $row) {
                $row->url = base_url('drive/files/explore', ['folder' => $row->name]);

                $folderPath = $rootPath . str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $row->name) . DIRECTORY_SEPARATOR;

                if(is_dir($folderPath)) {
                    $row->size = byte_format((new SplDirectoryInfo($folderPath))->getSize());
                }
            }

            return $result;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Documents::getDocumentsFolders
     */
    public function getDocumentsFolders()
    {
        $folders = ['Letters', 'Contracts', 'Securities',  'Quotations', 'Proposals'];

        foreach($folders as $folder) {
            $result[] = new SplArrayObject([
                'name' => $folder,
                'url' => base_url('drive/files/explore', ['folder' => $folder])
            ]);
        }

        return $result;
    }
}