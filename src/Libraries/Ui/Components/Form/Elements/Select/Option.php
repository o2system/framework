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

namespace O2System\Framework\Libraries\Ui\Components\Form\Elements\Select;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Option
 *
 * @package O2System\Framework\Libraries\Ui\Contents\Input
 */
class Option extends Element
{
    public function __construct($attributes = [])
    {
        parent::__construct('option');

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $this->attributes->addAttribute($name, $value);
            }
        }
    }

    public function disabled()
    {
        $this->attributes->addAttribute('disabled', 'disabled');

        return $this;
    }

    public function selected()
    {
        $this->attributes->addAttribute('selected', 'selected');

        return $this;
    }
}