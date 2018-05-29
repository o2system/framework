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

namespace O2System\Framework\Http\Presenter;

// ------------------------------------------------------------------------

use O2System\Psr\Patterns\Structural\Composite\RenderableInterface;
use O2System\Psr\Patterns\Structural\Repository\AbstractRepository;
use O2System\Spl\Info\SplClassInfo;

/**
 * Class Widget
 *
 * @package O2System\Framework\Http\Presenter
 */
class Widget extends AbstractRepository implements RenderableInterface
{
    public function getClassInfo()
    {
        $classInfo = new SplClassInfo($this);

        return $classInfo;
    }

    /**
     * Widget::__toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Widget::render
     *
     * @return string
     */
    public function render(array $options = [])
    {

    }
}