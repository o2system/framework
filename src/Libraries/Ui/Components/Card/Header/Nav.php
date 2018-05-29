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
    const HEADER_PILLS = 0;
    const HEADER_TABS = 1;

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

    public function createLink($label, $href)
    {
        $link = new Link($label, $href);
        $link->attributes->addAttributeClass('nav-link');

        return $this->createList($link);
    }
}