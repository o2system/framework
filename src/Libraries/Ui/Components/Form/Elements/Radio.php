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

namespace O2System\Framework\Libraries\Ui\Components\Form\Elements;

// ------------------------------------------------------------------------

/**
 * Class Radio
 * @package O2System\Framework\Libraries\Ui\Components\Form
 */
class Radio extends Checkbox
{
    /**
     * Radio::__construct
     *
     * @param string|null  $label
     * @param array        $attributes
     */
    public function __construct($label = null, array $attributes = [])
    {
        parent::__construct();
        $this->input->attributes->addAttribute('type', 'radio');
    }
}