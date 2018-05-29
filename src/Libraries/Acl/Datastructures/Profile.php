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

namespace O2System\Framework\Libraries\Acl\Datastructures;

// ------------------------------------------------------------------------

use O2System\Framework\Datastructures\Commons\Metadata;
use O2System\Framework\Datastructures\Commons\Name;
use O2System\Framework\Libraries\Acl\Datastructures\Profile\Images;
use O2System\Psr\Patterns\Structural\Repository\AbstractRepository;

/**
 * Class Profile
 *
 * @package O2System\Framework\Libraries\Acl\Datastructures
 */
class Profile extends AbstractRepository
{
    /**
     * Profile::__construct
     *
     * @param array $profile
     */
    public function __construct($profile = [])
    {
        $defaultProfile = [
            'id'         => isset($profile[ 'id' ]) ? $profile[ 'id' ] : null,
            'username'   => isset($profile[ 'username' ]) ? $profile[ 'username' ] : null,
            'name'       => [
                'first'   => isset($profile[ 'name_first' ]) ? $profile[ 'name_first' ] : null,
                'middle'  => isset($profile[ 'name_middle' ]) ? $profile[ 'name_middle' ] : null,
                'last'    => isset($profile[ 'name_last' ]) ? $profile[ 'name_last' ] : null,
                'display' => isset($profile[ 'name_display' ]) ? $profile[ 'name_display' ] : null,
            ],
            'images'     => [
                'avatar' => isset($profile[ 'avatar' ]) ? $profile[ 'avatar' ] : null,
                'cover'  => isset($profile[ 'cover' ]) ? $profile[ 'cover' ] : null,
            ],
            'gender'     => isset($profile[ 'gender' ]) ? $profile[ 'gender' ] : 'UNDEFINED',
            'age'        => 'UNDEFINED',
            'birthplace' => isset($profile[ 'birthplace' ]) ? $profile[ 'birthplace' ] : 'UNDEFINED',
            'birthday'   => isset($profile[ 'birthday' ]) ? $profile[ 'birthday' ] : '0000-00-00',
            'marital'    => isset($profile[ 'marital' ]) ? $profile[ 'marital' ] : 'UNDEFINED',
            'religion'   => isset($profile[ 'religion' ]) ? $profile[ 'religion' ] : 'UNDEFINED',
            'biography'  => isset($profile[ 'biography' ]) ? $profile[ 'biography' ] : null,
            'metadata'   => isset($profile[ 'metadata' ]) ? $profile[ 'metadata' ] : [],
        ];

        if (empty($defaultProfile[ 'name' ][ 'display' ])) {
            $defaultProfile[ 'name' ][ 'display' ] = implode(' ', array_filter([
                $defaultProfile[ 'name' ][ 'first' ],
                $defaultProfile[ 'name' ][ 'middle' ],
                $defaultProfile[ 'name' ][ 'last' ],
            ]));
        }

        foreach ($defaultProfile as $item => $value) {
            $this->store($item, $value);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Profile::store
     *
     * @param string $offset
     * @param mixed  $value
     */
    public function store($offset, $value)
    {
        if ($offset === 'name' and is_array($value)) {
            $value = new Name($value);
        } elseif ($offset === 'metadata' and is_array($value)) {
            $value = new Metadata($value);
        } elseif ($offset === 'images' and is_array($value)) {
            $images = new Images();

            $filePath = PATH_STORAGE . 'users' . DIRECTORY_SEPARATOR . $this->offsetGet('username') . DIRECTORY_SEPARATOR;
            $images->setFilePath($filePath);

            foreach ($value as $key => $image) {
                $images->store($key, $image);
            }
            $value = $images;
        } elseif ($offset === 'gender') {
            if (in_array($value, ['MALE', 'FEMALE', 'UNDEFINED'])) {
                $value = strtoupper($value);
            } else {
                return;
            }
        } elseif ($offset === 'marital') {
            if (in_array($value, ['SINGLE', 'MARRIED', 'DIVORCED', 'UNDEFINED'])) {
                $value = strtoupper($value);
            } else {
                return;
            }
        } elseif ($offset === 'religion') {
            if (in_array($value, ['HINDU', 'BUDDHA', 'MOSLEM', 'CHRISTIAN', 'CATHOLIC', 'UNDEFINED'])) {
                $value = strtoupper($value);
            } else {
                return;
            }
        } elseif ($offset === 'birthday' and $value !== '0000-00-00') {
            $value = date('Y-m-d', strtotime($value));
        } elseif ($offset === 'biography') {
            $value = trim($value);
        }

        $this->storage[ $offset ] = $value;
    }
}