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

namespace O2System\Framework\Libraries\Ui\Components\Navbar;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Contents\Link;

class Links
{
    /**
     * Links::NAVBAR_LEFT
     *
     * @var int
     */
    const NAVBAR_LEFT = 0;

    /**
     * Links::NAVBAR_RIGHT
     *
     * @var int
     */
    const NAVBAR_RIGHT = 1;

    /**
     * Links::$left
     *
     * @var \O2System\Framework\Libraries\Ui\Components\Navbar\Nav
     */
    public $left;

    /**
     * Links::$right
     *
     * @var \O2System\Framework\Libraries\Ui\Components\Navbar\Nav
     */
    public $right;

    // ------------------------------------------------------------------------

    /**
     * Links::__construct
     */
    public function __construct()
    {
        $this->left = new Nav();
        $this->left->attributes->addAttributeClass('navbar-left');

        $this->right = new Nav();
        $this->right->attributes->addAttributeClass('navbar-right');
    }

    // ------------------------------------------------------------------------

    /**
     * Links::createLink
     *
     * @param string|Link    $label
     * @param string|null    $href
     * @param int            $position
     *
     * @return mixed
     */
    public function createLink($label, $href = null, $position = self::NAVBAR_LEFT)
    {
        if ( ! is_object($label) && isset($href)) {
            $label = new Link($label, $href);
        }

        switch ($position) {
            default:
            case self::NAVBAR_LEFT:
                $node = $this->left->createList($label);
                break;

            case self::NAVBAR_RIGHT:
                $node = $this->right->createList($label);
                break;
        }

        return $node;
    }
}