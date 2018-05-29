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

namespace O2System\Framework\Libraries\Ui\Components\Buttons;

/**
 * Class Dropdown
 *
 * @package O2System\Framework\Libraries\Ui\Components\Buttons
 */
class Dropdown extends \O2System\Framework\Libraries\Ui\Components\Dropdown
{
    public $toggle;
    public $toggleButton;

    public function __construct($label = null)
    {
        parent::__construct($label);

        $this->attributes->removeAttributeClass('dropdown');
        $this->attributes->addAttributeClass('btn-group');
        $this->attributes->addAttribute('role', 'group');
    }
}