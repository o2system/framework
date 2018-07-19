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

namespace O2System\Framework\Http\Presenter;

// ------------------------------------------------------------------------

use O2System\Psr\Patterns\Structural\Repository\AbstractRepository;

/**
 * Class Manifest
 * @package O2System\Framework\Http\Presenter
 */
class Manifest extends AbstractRepository implements \JsonSerializable
{
    public function __toJson()
    {
        return json_encode($this->jsonSerialize(), JSON_PRETTY_PRINT);
    }
}