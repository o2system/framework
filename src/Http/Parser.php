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

namespace O2System\Framework\Http;

// ------------------------------------------------------------------------

use O2System\Parser\Drivers;

/**
 * Class Parser
 *
 * @package O2System\Framework\Http
 */
class Parser extends Drivers
{
    public function __construct()
    {
        $config = config()->loadFile('parser', true);

        parent::__construct($config);
    }
}