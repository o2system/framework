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

use O2System\Psr\Patterns\Structural\Repository\AbstractRepository;

/**
 * Class Account
 *
 * @package O2System\Framework\Libraries\Acl\Datastructures
 */
class Account extends AbstractRepository
{
    /**
     * Account::__construct
     *
     * @param array $account
     * @param bool  $hash Hashed password and pin options.
     */
    public function __construct($account = [], $hash = true)
    {
        $defaultAccount = [
            'id'       => null,
            'email'    => null,
            'msisdn'   => null,
            'username' => null,
            'password' => null,
            'pin'      => null,
            'token'    => null
        ];

        foreach ($defaultAccount as $item => $value) {
            if (in_array($item, ['password', 'pin']) and $hash === true) {
                if (isset($account[ $item ])) {
                    $config = config('acl', true);

                    if (empty($config)) {
                        $this->store(
                            $item,
                            password_hash($account[ $item ], PASSWORD_DEFAULT)
                        );
                    } else {

                        if ( ! empty($config->options)) {
                            $config->algorithm = PASSWORD_BCRYPT;
                        }

                        $this->store(
                            $item,
                            password_hash($account[ $item ], $config->algorithm, $config->options)
                        );
                    }
                }
            } else {
                $this->store($item, (isset($account[ $item ]) ? $account[ $item ] : null));
            }
        }
    }

    public function store($offset, $value)
    {
        if ($offset === 'profile') {
            if (empty($this->storage[ 'profile' ])) {
                $value = new Profile($value);
            }
        } elseif ($offset === 'role') {
            $value = new Roles\Role($value);
        }

        $this->storage[ $offset ] = $value;
    }
}