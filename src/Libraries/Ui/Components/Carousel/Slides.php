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

namespace O2System\Framework\Libraries\Ui\Components\Carousel;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Slides
 *
 * @package O2System\Framework\Libraries\Ui\Components\Carousel
 */
class Slides extends Element
{
    /**
     * Slides::$indicators
     *
     * @var \O2System\Framework\Libraries\Ui\Components\Carousel\Indicators
     */
    private $indicators;

    /**
     * Slides::$target
     *
     * @var string
     */
    private $target;

    // ------------------------------------------------------------------------

    /**
     * Slides::__construct
     */
    public function __construct()
    {
        parent::__construct('div', 'slides');
        $this->attributes->addAttributeClass('carousel-inner');
        $this->attributes->addAttribute('role', 'listbox');
    }

    // ------------------------------------------------------------------------

    /**
     * Slides::setTarget
     *
     * @param string $target
     *
     * @return static
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Slides::setIndicators
     *
     * @param Indicators $indicators
     *
     * @return static
     */
    public function setIndicators(Indicators &$indicators)
    {
        $this->indicators =& $indicators;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Slides::createSlide
     *
     * @return Slide
     */
    public function createSlide()
    {
        $this->childNodes->push(new Slide());

        $slideNo = $this->childNodes->key();
        $indicator = $this->indicators->childNodes->createNode('li');
        $indicator->entity->setEntityName('indicator-' . $slideNo);
        $indicator->attributes->addAttribute('data-target', '#' . $this->target);
        $indicator->attributes->addAttribute('data-slide-to', $slideNo);

        return $this->childNodes->last();
    }
}