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

namespace O2System\Framework\Libraries\Ui\Components;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components\Carousel\Control;
use O2System\Framework\Libraries\Ui\Components\Carousel\Indicators;
use O2System\Framework\Libraries\Ui\Components\Carousel\Slides;
use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Carousel
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class Carousel extends Element
{
    public $indicators;
    public $control;
    public $slides;

    public function __construct($id = null)
    {
        parent::__construct('div', 'carousel');
        $this->attributes->addAttributeClass('carousel slide');
        $this->attributes->addAttribute('data-ride', 'carousel');

        $id = empty($id) ? 'carousel-' . mt_rand(1, 1000) : $id;
        $this->attributes->setAttributeId($id);

        $this->indicators = new Indicators();
        $this->childNodes->push($this->indicators);

        $this->slides = new Slides();
        $this->slides
            ->setIndicators($this->indicators)
            ->setTarget($this->attributes->getAttributeId());
        $this->childNodes->push($this->slides);

        $this->control = new Control();
        $this->control->left->setAttributeHref('#' . $id);
        $this->control->right->setAttributeHref('#' . $id);
        $this->childNodes->push($this->control);
    }
}