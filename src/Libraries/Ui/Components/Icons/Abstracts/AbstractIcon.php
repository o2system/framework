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

namespace O2System\Framework\Libraries\Ui\Components\Icons\Abstracts;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Glyphicon
 *
 * @package O2System\Framework\Libraries\Ui\Contents\Icons
 */
abstract class AbstractIcon extends Element
{
    protected $iconPrefixClass;

    public function __construct($iconName = null)
    {
        parent::__construct('span');

        if (isset($iconName)) {
            $this->setClass($iconName);
        }
    }

    public function setClass($className)
    {
        $this->attributes->removeAttributeClass($this->iconPrefixClass . '-*');
        $this->attributes->addAttributeClass($this->iconPrefixClass . '-' . $className);

        return $this;
    }
}