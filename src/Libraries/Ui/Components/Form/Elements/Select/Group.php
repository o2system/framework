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
 * Class Group
 *
 * @package O2System\Framework\Libraries\Ui\Contents\Input
 */
class Group extends Element
{
    use Traits\OptionCreateTrait;

    public function __construct($attributes = [])
    {
        parent::__construct('optgroup');

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
}