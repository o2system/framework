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

namespace O2System\Framework\Models\Sql\System\Users;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\System\Users;
use O2System\Framework\Models\Sql\Model;

/**
 * Class Profiles
 * @package O2System\Framework\Models\Sql\System\Users
 */
class Profiles extends Model
{
    /**
     * Profile::$table
     *
     * @var string
     */
    public $table = 'sys_users_profiles';

    /**
     * Profile::$visibleColumns
     *
     * @var array
     */
    public $appendColumns = [
        'avatar_url'
    ];

    // ------------------------------------------------------------------------

    /**
     * Profiles::$uploadedImageKey
     *
     * @var string
     */
    public $uploadedImageKey = 'avatar';

    // ------------------------------------------------------------------------

    /**
     * Profiles::$uploadedImageFilePath
     *
     * @var array
     */
    public $uploadedImageFilePath = PATH_STORAGE. 'images/system/users/profiles/';

    // ------------------------------------------------------------------------

    /**
     * Profiles::user
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function user()
    {
        return $this->belongsTo(Users::class, 'id_sys_user');
    }

    // ------------------------------------------------------------------------

    /**
     * Profiles::avatar_url
     *
     * @return string
     */
    public function avatar_url()
    {
        if(is_file($filePath = $this->uploadedImageFilePath . $this->row->avatar)) {
            return images_url($filePath);
        }

        return base_url('resources/themes/default/assets/img/avatar.jpg');
    }
}
