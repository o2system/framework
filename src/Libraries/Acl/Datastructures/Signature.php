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
 * Class Signature
 * @package O2System\Framework\Libraries\Acl\Datastructures
 */
class Signature extends AbstractRepository
{
    public function __construct(array $signature = [])
    {
        $defaultSignature = [
            'id_sys_user' => null,
            'code'        => null,
            'ip_address'  => server_request()->getClientIpAddress(),
            'user_agent'  => server_request()->getClientUserAgent(),
        ];

        foreach ($defaultSignature as $item => $value) {
            if (array_key_exists($item, $signature)) {
                $this->store($item, $signature[ $item ]);
            } else {
                $this->store($item, $value);
            }
        }
    }
}