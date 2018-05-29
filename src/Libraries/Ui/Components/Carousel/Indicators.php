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

namespace O2System\Framework\Libraries\Ui\Components\Carousel;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Contents\Lists\Ordered;

/**
 * Class Indicators
 *
 * @package O2System\Framework\Libraries\Ui\Components\Carousel
 */
class Indicators extends Ordered
{
    public function __construct()
    {
        parent::__construct();
        $this->attributes->addAttributeClass('carousel-indicators');
    }
}