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
    /**
     * AbstractIcon::$iconPrefixClass
     *
     * @var string
     */
    protected $iconPrefixClass;

    // ------------------------------------------------------------------------

    /**
     * AbstractIcon::__construct
     *
     * @param string|null $iconName
     */
    public function __construct($iconName = null)
    {
        parent::__construct('span');

        if (isset($iconName)) {
            $this->setClass($iconName);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractIcon::setClass
     *
     * @param string $className
     *
     * @return static
     */
    public function setClass($className)
    {
        $this->attributes->removeAttributeClass($this->iconPrefixClass . '-*');
        $this->attributes->addAttributeClass($this->iconPrefixClass . '-' . $className);

        return $this;
    }
}