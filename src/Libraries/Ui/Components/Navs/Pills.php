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

namespace O2System\Framework\Libraries\Ui\Components\Navs;

// ------------------------------------------------------------------------

/**
 * Class Pills
 *
 * @package O2System\Framework\Libraries\Ui\Components\Navs
 */
class Pills extends Base
{
    public function __construct()
    {
        parent::__construct();

        $this->attributes->addAttributeClass('nav-pills');
    }

    public function justified()
    {
        $this->attributes->addAttributeClass('nav-justified');

        return $this;
    }

    public function verticalStacked()
    {
        $this->attributes->addAttributeClass('nav-stacked');

        return $this;
    }
}