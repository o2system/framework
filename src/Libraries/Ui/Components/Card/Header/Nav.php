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

namespace O2System\Framework\Libraries\Ui\Components\Card\Header;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components\Card\Header\Nav\Link;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;

/**
 * Class Nav
 *
 * @package O2System\Framework\Libraries\Ui\Components\Card\Header
 */
class Nav extends Unordered
{
    /**
     * Nav::HEADER_PILLS
     *
     * @var int
     */
    const HEADER_PILLS = 0;

    /**
     * Nav::HEADER_TABS
     *
     * @var int
     */
    const HEADER_TABS = 1;

    // ------------------------------------------------------------------------

    /**
     * Nav::__construct
     *
     * @param int $type
     */
    public function __construct($type = self::HEADER_PILLS)
    {
        parent::__construct();

        switch ($type) {
            default:
            case self::HEADER_PILLS:
                $this->attributes->addAttributeClass(['nav', 'nav-pills', 'card-header-pills']);
                break;
            case self::HEADER_TABS;
                $this->attributes->addAttributeClass(['nav', 'nav-tabs', 'card-header-tabs']);
                break;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Nav::createLink
     *
     * @param string $label
     * @param string $href
     *
     * @return \O2System\Framework\Libraries\Ui\Contents\Lists\Item
     */
    public function createLink($label, $href)
    {
        $link = new Link($label, $href);
        $link->attributes->addAttributeClass('nav-link');

        return $this->createList($link);
    }
}