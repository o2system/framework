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

namespace O2System\Framework\Libraries\Ui\Admin;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Sidebar
 * @package O2System\Framework\Libraries\Ui\Admin
 */
class Sidebar extends Element
{
    /**
     * Sidebar::$menu
     *
     * @var \O2System\Framework\Libraries\Ui\Admin\Sidebar\Menu
     */
    public $menu;

    // ------------------------------------------------------------------------

    /**
     * Code::__construct
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct('div');

        if (isset($attributes[ 'id' ])) {
            $this->entity->setEntityName($attributes[ 'id' ]);
        }

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $this->attributes->addAttribute($name, $value);
            }
        }

        $this->menu = new Sidebar\Menu();

        $this->childNodes->push($this->menu);
    }
}