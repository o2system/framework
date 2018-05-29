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

namespace O2System\Framework\Libraries\Ui\Components\Form\Elements\Select\Traits;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components\Form\Elements\Select\Option;

/**
 * Trait OptionCreateTrait
 *
 * @package O2System\Framework\Libraries\Ui\Contents\Form\Select\Traits
 */
trait OptionCreateTrait
{
    public function createOptions(array $options, $selected = null)
    {
        foreach ($options as $label => $value) {
            $option = $this->createOption($label, $value);

            if (is_array($selected) && in_array($value, $selected)) {
                $option->selected();
            } elseif ($selected === $value) {
                $option->selected();
            }
        }

        return $this;
    }

    public function createOption($label, $value = null, $selected = false)
    {
        $option = new Option();
        $option->textContent->push($label);

        if (isset($value)) {
            $option->attributes->addAttribute('value', $value);
        }

        if ($selected) {
            $option->selected();
        }

        $this->childNodes->push($option);

        return $this->childNodes->last();
    }
}