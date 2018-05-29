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
 * Class Credential
 * @package O2System\Framework\Libraries\Acl\Datastructures
 */
class Credential extends AbstractRepository
{
    public function __construct(array $credential = [])
    {
        $defaultCredential = [
            'id_sys_user' => null,
            'token'       => null,
            'ip_address'  => server_request()->getClientIpAddress(),
            'user_agent'  => server_request()->getClientUserAgent(),
        ];

        foreach ($defaultCredential as $item => $value) {
            if (array_key_exists($item, $credential)) {
                $this->store($item, $credential[ $item ]);
            } else {
                $this->store($item, $value);
            }
        }
    }
}