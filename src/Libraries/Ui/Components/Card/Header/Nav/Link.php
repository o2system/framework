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

namespace O2System\Framework\Libraries\Ui\Components\Card\Header\Nav;

// ------------------------------------------------------------------------

/**
 * Class Link
 *
 * @package O2System\Framework\Libraries\Ui\Components
 *
 * @todo    : add collapse bootstrap 4.0 and add tooltip
 */
class Link extends \O2System\Framework\Libraries\Ui\Contents\Link
{
    public function __construct($label = null, $href = null)
    {
        parent::__construct($label, $href);
        $this->attributes->addAttributeClass('nav-link');
    }

    // ------------------------------------------------------------------------

    public function active()
    {
        $this->attributes->addAttributeClass('active');

        return $this;
    }

    public function disabled()
    {
        $this->attributes->addAttributeClass('disabled');

        return $this;
    }
}