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

namespace O2System\Framework\Libraries\Ui\Contents\Table;

// ------------------------------------------------------------------------

/**
 * Class Header
 *
 * @package O2System\Framework\Libraries\Ui\Contents\Table
 */
class Header extends Body
{
    public function __construct()
    {
        parent::__construct();
        $this->tagName = 'thead';
    }
}