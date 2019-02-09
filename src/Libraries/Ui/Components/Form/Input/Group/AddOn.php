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

namespace O2System\Framework\Libraries\Ui\Components\Form\Input\Group;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components\Button;
use O2System\Framework\Libraries\Ui\Element;

/**
 * Class AddOn
 *
 * @package O2System\Framework\Libraries\Ui\Components\Input
 */
class AddOn extends Element
{
    /**
     * AddOn::ADDON_LEFT
     *
     * @var int
     */
    const ADDON_LEFT = 0;

    /**
     * AddOn::ADDON_RIGHT
     *
     * @var int
     */
    const ADDON_RIGHT = 1;

    /**
     * AddOn::$position
     *
     * @var int
     */
    public $position = 0;

    // ------------------------------------------------------------------------

    /**
     * AddOn::__construct
     *
     * @param int $position
     */
    public function __construct($position = self::ADDON_LEFT)
    {
        parent::__construct('span');
        $this->attributes->addAttributeClass('input-group-text');
        $this->setPosition($position);
    }

    // ------------------------------------------------------------------------

    /**
     * AddOn::setPosition
     *
     * @param $position
     *
     * @return static
     */
    public function setPosition($position)
    {
        if (in_array($position, [self::ADDON_LEFT, self::ADDON_RIGHT])) {
            $this->position = $position;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * AddOn::render
     *
     * @return string
     */
    public function render()
    {
        if ($this->hasChildNodes()) {
            if ($this->childNodes->first() instanceof Button) {
                $this->attributes->removeAttributeClass('input-group-text');
            }
        }

        return parent::render();
    }
}