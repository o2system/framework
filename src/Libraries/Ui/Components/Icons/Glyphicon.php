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

namespace O2System\Framework\Libraries\Ui\Components\Icons;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components\Icons\Abstracts\AbstractIcon;

/**
 * Class Glyphicon
 *
 * @package O2System\Framework\Libraries\Ui\Contents\Icons
 */
class Glyphicon extends AbstractIcon
{
    /**
     * Glyphicon::__construct
     *
     * @param string|null $iconName
     */
    public function __construct($iconName = null)
    {
        $this->iconPrefixClass = 'glyphicon';
        parent::__construct($iconName);
    }
}