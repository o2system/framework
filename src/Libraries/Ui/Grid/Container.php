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

namespace O2System\Framework\Libraries\Ui\Grid;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Container
 * @package O2System\Framework\Libraries\Ui\Grid
 */
class Container extends Element
{
    /**
     * Container::$fluid
     *
     * Full width container mode flag.
     *
     * @var bool
     */
    public $fluid = false;

    // ------------------------------------------------------------------------

    /**
     * Container::__construct
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct('div');
        $this->attributes->addAttributeClass('container');

        if (isset($attributes[ 'id' ])) {
            $this->entity->setEntityName($attributes[ 'id' ]);
        }

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $this->attributes->addAttribute($name, $value);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Container::fluid
     *
     * @param bool $fluid
     *
     * @return static
     */
    public function fluid($fluid)
    {
        $this->fluid = (bool)$fluid;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Container::render
     *
     * @return string
     */
    public function render()
    {
        if ($this->fluid) {
            $this->attributes->removeAttributeClass('container');
            $this->attributes->addAttributeClass('container-fluid');
        }

        return parent::render();
    }
}