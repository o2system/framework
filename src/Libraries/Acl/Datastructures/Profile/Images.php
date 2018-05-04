<?php
/**
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */

// ------------------------------------------------------------------------

namespace O2System\Framework\Libraries\Acl\Datastructures\Profile;

// ------------------------------------------------------------------------

use O2System\Psr\Patterns\Structural\Repository\AbstractRepository;

/**
 * Class Images
 *
 * @package O2System\Framework\Libraries\Acl\Datastructures\Profile
 */
class Images extends AbstractRepository
{
    protected $filePath;

    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function store($offset, $value)
    {
        $filePath = $this->filePath . 'images' . DIRECTORY_SEPARATOR . $value;

        if (is_file($filePath)) {
            $value = storage_url($filePath);
        } else {
            $value = assets_url('img' . DIRECTORY_SEPARATOR . $offset . '.jpg');
        }

        parent::store($offset, $value);
    }
}