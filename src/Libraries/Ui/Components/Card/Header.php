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

namespace O2System\Framework\Libraries\Ui\Components\Card;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components\Card\Header\Nav;
use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Header
 *
 * @package O2System\Framework\Libraries\Ui\Components\Card
 */
class Header extends Element
{
    public $nav;
    public $options;

    public function __construct()
    {
        parent::__construct('div', 'header');
        $this->attributes->addAttributeClass('card-header');
    }

    /**
     * Header::createNav
     *
     * @return Nav
     */
    public function createNav($type = Nav::HEADER_PILLS)
    {
        $nav = new Nav($type);
        $this->childNodes->push($nav);

        return $this->nav = $this->childNodes->last();
    }

    /**
     * Header::createOptions
     *
     * @return Nav
     */
    public function createOptions()
    {
        $nav = new Nav(Nav::HEADER_PILLS);
        $nav->attributes->addAttributeClass('float-right');
        $this->childNodes->push($nav);

        return $this->options = $this->childNodes->last();
    }
}