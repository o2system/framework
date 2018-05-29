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

namespace O2System\Framework\Datastructures\Commons;

// ------------------------------------------------------------------------

use O2System\Psr\Patterns\Structural\Repository\AbstractRepository;

/**
 * Class Name
 *
 * @package O2System\Framework\Datastructures\Commons
 */
class Name extends AbstractRepository
{
    /**
     * Name::__construct
     *
     * @param string $name
     */
    public function __construct($name)
    {
        if (is_string($name)) {
            $parts = explode(' ', trim($name));
            $name = [];

            if (count($parts) == 1) {
                $name[ 'first' ] = $parts[ 0 ];
                $name[ 'middle' ] = null;
                $name[ 'last' ] = null;
            } elseif (count($parts) == 2) {
                $name[ 'first' ] = $parts[ 0 ];
                $name[ 'middle' ] = null;
                $name[ 'last' ] = $parts[ 1 ];
            } elseif (count($parts) == 3) {
                $name[ 'first' ] = $parts[ 0 ];
                $name[ 'middle' ] = $parts[ 1 ];
                $name[ 'last' ] = $parts[ 2 ];
            } else {
                $name[ 'first' ] = $parts[ 0 ];
                $name[ 'middle' ] = $parts[ 1 ];

                $parts = array_slice($parts, 2);
                $name[ 'last' ] = implode(' ', $parts);
            }
        }

        foreach ($name as $key => $value) {
            $this->store($key, $value);
        }
    }

    public function __toString()
    {
        $name = $this->offsetGet('first');

        if ($this->offsetExists('middle')) {
            $name .= ' ' . $this->offsetGet('middle');
        }

        if ($this->offsetExists('last')) {
            $name .= ' ' . $this->offsetGet('last');
        }

        return (string)trim($name);
    }
}