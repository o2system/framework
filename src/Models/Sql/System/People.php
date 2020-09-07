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
namespace O2System\Framework\Models\Sql\System;

// ------------------------------------------------------------------------
use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\MetadataTrait;
// ------------------------------------------------------------------------
/**
 * Class People
 * @package O2System\Framework\Models\Sql\System
 */
class People extends Model
{
    use MetadataTrait;

    /**
     * People::$table
     *
     * @var string
     */
    public $table = 'sys_people';
    // ------------------------------------------------------------------------
    /**
     * People::$insertValidationRules
     *
     * @var array
     */
    public $insertValidationRules = [
        'fullname' => 'required',
        'avatar' => 'optional',
        'cover' => 'optional',
        'gender' => 'required|listed[MALE, FEMALE]',
    ];

    // ------------------------------------------------------------------------
    /**
     * People::$insertValidationCustomErrors
     *
     * @var array
     */
    public $insertValidationCustomErrors = [
        'fullname' => [
            'required' => 'People full name id cannot be empty!',
        ],
        'gender' => [
            'required' => 'People gender cannot be empty!',
            'listed' => 'Gender data must be listed: FEMALE or MALE'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * People::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'fullname' => 'required',
        'avatar' => 'optional',
        'cover' => 'optional',
        'gender' => 'required',
    ];

    // ------------------------------------------------------------------------

    /**
     * People::$updateValidationCustomErrors
     *
     * @var array
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'People id cannot be empty!',
            'integer' => 'People id data must be an integer'
        ],
        'fullname' => [
            'required' => 'People full name id cannot be empty!',
        ],
        'gender' => [
            'required' => 'People gender cannot be empty!',
            'listed' => 'Gender data must be listed: FEMALE or MALE'
        ],
    ];

    /**
     * People::$uploadFilePaths
     *
     * @var array
     */
    /*
    public $uploadFilePaths = [
        'avatar' => PATH_STORAGE . 'images' . DIRECTORY_SEPARATOR . 'people' . DIRECTORY_SEPARATOR,
        'cover' => PATH_STORAGE . 'images' . DIRECTORY_SEPARATOR . 'people' . DIRECTORY_SEPARATOR
    ]; */

    // ------------------------------------------------------------------------
    /**
     * People::$fillableColumns
     *
     * @var array
     */
    public $fillableColumns = [
        'id',
        'fullname',
        'avatar',
        'cover',
        'gender',
        'record_status',
        'record_language',
        'record_type',
        'record_visibility',
        'record_create_user',
        'record_create_timestamp',
        'record_update_user',
        'record_update_timestamp'
    ];

    /**
     * People::$appendColumns
     *
     * @var string
     */
    public $appendColumns = [
        'avatar_url'
    ];

    // ------------------------------------------------------------------------

    /**
     * Poeple::beforeInsertOrUpdate
     *
     * @param mixed $data
     */
    public function beforeInsertOrUpdate(&$data)
    {
        // Mutate avatar
        if(isset($data->avatar)) {
            $data->avatar = pathinfo($data->avatar, PATHINFO_BASENAME);
        }

        // Mutate cover
        if(isset($data->cover)) {
            $data->cover = pathinfo($data->avatar, PATHINFO_BASENAME);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * People::user
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function user()
    {
        return $this->morphByOneThrough(Users::class, Relationships::class, 'relation');
    }

    // ------------------------------------------------------------------------

    /**
     * People::avatar_url
     *
     * @return string
     */
    public function avatar_url()
    {
        if (is_file($filePath = PATH_STORAGE . 'images' . DIRECTORY_SEPARATOR . $this->row->avatar)) {
            return images_url($filePath);
        }

        if (is_file($avatarFilePath = PATH_STORAGE . 'images/default/avatar-' . strtolower($this->row->gender) . '.png')) {
            return images_url($avatarFilePath);
        } elseif (is_file($avatarFilePath = PATH_STORAGE . 'images/default/avatar.png')) {
            return images_url($avatarFilePath);
        }
    }

    // ------------------------------------------------------------------------
    /**
     * People::afterInsertOrUpdate
     *
     * @param mixed $data
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     */
    // public function afterInsertOrUpdate(&$data)
    // {
    //     if(isset($data['username'])) {
    //         $user = new Data([
    //             'email' => $data['email'] ?? null,
    //             'username' => $data['username'] ?? null,
    //             'msisdn' => $data['msisdn'] ?? null,
    //             'password' => $data['password'] ?? null,
    //             'password_confirm' => $data['password_confirm'] ?? null,
    //             'pin' => $data['pin'] ?? null,
    //         ]);

    //         /** Insert user */
    //         if(models(Users::class)->insertOrUpdate($user)) {
    //             /** Insert people-to-users relationship */
    //             if( ! models(Relationships::class)->insertOrUpdate(new Data([
    //                 'ownership_id' => $user->id,
    //                 'ownership_model' => Users::class,
    //                 'relation_id' => $data->id,
    //                 'relation_model' => People::class,
    //                 'relation_role' => 'PROFILE'
    //             ]))) {
    //                 return false;
    //             }
    //         }
    //     }

    //     if(isset($data['type'])) {
    //         if($taxonomy = models(Taxonomies::class)->findWhere([
    //             'slug' => strtolower($data['type'])
    //         ], 1)) {
    //             /** Insert people-to-taxonomies relationship */
    //             if( ! models(Relationships::class)->insertOrUpdate(new Data([
    //                 'ownership_id' => $data->id,
    //                 'ownership_model' => People::class,
    //                 'relation_id' => $taxonomy->id,
    //                 'relation_model' => Taxonomies::class,
    //                 'relation_role' => strtoupper($data['type'])
    //             ]))) {
    //                 return false;
    //             }
    //         }
    //     }

    //     return true;
    // }

}
