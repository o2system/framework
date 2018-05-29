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

namespace O2System\Framework\Libraries\Ui\Components\Form\Elements;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Output
 *
 * @package O2System\Framework\Libraries\Ui\Contents\Form\Elements
 */
class Output extends Element
{
    /**
     * Output::__construct
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct('output');

        if (isset($attributes[ 'id' ])) {
            $this->entity->setEntityName('output-' . $attributes[ 'id' ]);
        } elseif (isset($attributes[ 'name' ])) {
            $this->entity->setEntityName('output-' . $attributes[ 'name' ]);
        }

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $this->attributes->addAttribute($name, $value);
            }
        }
    }
}