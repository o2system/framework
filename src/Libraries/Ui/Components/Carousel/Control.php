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

namespace O2System\Framework\Libraries\Ui\Components\Carousel;

// ------------------------------------------------------------------------
use O2System\Framework\Libraries\Ui\Contents\Link;
use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Control
 *
 * @package O2System\Framework\Libraries\Ui\Components\Carousel
 */
class Control extends Element
{
    public $left;
    public $right;

    public function __construct()
    {
        parent::__construct('div', 'control');
        $this->attributes->addAttributeClass('carousel-controls');

        $this->left = new Link();
        $this->left->attributes->addAttributeClass('carousel-control-prev');
        $this->left->attributes->addAttribute('data-slide', 'prev');

        $icon = new Element('span', 'icon');
        $icon->attributes->addAttributeClass('carousel-control-prev-icon');

        $srOnly = new Element('span', 'sr-only');
        $srOnly->attributes->addAttributeClass('sr-only');

        $this->left->childNodes->push($icon);
        $this->left->childNodes->push($srOnly);

        $this->right = new Link();
        $this->right->attributes->addAttributeClass('carousel-control-next');
        $this->right->attributes->addAttribute('data-slide', 'next');

        $icon = new Element('span', 'icon');
        $icon->attributes->addAttributeClass('carousel-control-next-icon');

        $srOnly = new Element('span', 'sr-only');
        $srOnly->attributes->addAttributeClass('sr-only');

        $this->right->childNodes->push($icon);
        $this->right->childNodes->push($srOnly);
    }

    public function __toString()
    {
        return $this->render();
    }

    public function render()
    {
        $output[] = $this->left;
        $output[] = $this->right;

        return implode(PHP_EOL, $output);
    }
}