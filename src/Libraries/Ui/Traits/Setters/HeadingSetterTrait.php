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

namespace O2System\Framework\Libraries\Ui\Traits\Setters;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Contents\Heading;

/**
 * Trait HeadingSetterTrait
 *
 * @package O2System\Framework\Libraries\Ui\Traits\Setters
 */
trait HeadingSetterTrait
{
    public $heading;

    public function setHeading($text, $level = 3)
    {
        $this->heading = new Heading($text, $level);
        $this->heading->entity->setEntityName($text);

        return $this;
    }
}