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
    /**
     * Option::__construct
     *
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct('option');

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $this->attributes->addAttribute($name, $value);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Option::disabled
     *
     * @return static
     */
    public function disabled()
    {
        $this->attributes->addAttribute('disabled', 'disabled');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Option::selected
     *
     * @return static
     */
    public function selected()
    {
        $this->attributes->addAttribute('selected', 'selected');

        return $this;
    }
}