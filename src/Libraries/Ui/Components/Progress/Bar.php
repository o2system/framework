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

namespace O2System\Framework\Libraries\Ui\Components\Progress;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;
use O2System\Framework\Libraries\Ui\Interfaces\ContextualInterface;
use O2System\Framework\Libraries\Ui\Traits\Setters\ContextualClassSetterTrait;

/**
 * Class Bar
 *
 * @package O2System\Framework\Libraries\Ui\Components\Progress
 */
class Bar extends Element implements ContextualInterface
{
    use ContextualClassSetterTrait;

    /**
     * Bar::$label
     *
     * @var \O2System\Framework\Libraries\Ui\Element
     */
    protected $label;

    /**
     * Bar::$now
     *
     * @var int
     */
    protected $now = 0;

    /**
     * Bar::$min
     *
     * @var int
     */
    protected $min = 0;

    /**
     * Bar::$max
     *
     * @var int
     */
    protected $max = 100;

    /**
     * Bar::$withLabel
     *
     * @var bool
     */
    protected $withLabel = false;

    // ------------------------------------------------------------------------

    /**
     * Bar::__construct
     *
     * @param int    $now
     * @param int    $min
     * @param int    $max
     * @param string $contextualClass
     */
    public function __construct($now = 0, $min = 0, $max = 100, $contextualClass = 'primary')
    {
        parent::__construct('div');

        $this->attributes->addAttributeClass('progress-bar');
        $this->attributes->addAttribute('role', 'progressbar');

        $this->setContextualClassPrefix('bg');
        $this->setContextualClassSuffix($contextualClass);

        $this->label = new Element('span');
        $this->label->attributes->addAttributeClass('sr-only');

        $this->setNow($now);
        $this->setMin($min);
        $this->setMax($max);
    }

    // ------------------------------------------------------------------------

    /**
     * Bar::setNow
     *
     * @param int $number
     *
     * @return static
     */
    public function setNow($number)
    {
        $this->now = (int)$number;
        $this->attributes->addAttribute('aria-valuenow', $this->now);

        if ($this->now < 10) {
            $this->attributes->addAttribute('style',
                'min-width: ' . 3 . 'em; width: ' . $this->now . '%;');
        } else {
            $this->attributes->addAttribute('style', 'width: ' . $this->now . '%;');
        }

        $this->label->textContent->push($this->now . ' ' . language()->getLine('Complete'));

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Bar::setMin
     *
     * @param int $number
     *
     * @return static
     */
    public function setMin($number)
    {
        $this->min = (int)$number;
        $this->attributes->addAttribute('aria-valuemin', $this->min);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Bar::setMax
     *
     * @param int $number
     *
     * @return static
     */
    public function setMax($number)
    {
        $this->max = (int)$number;
        $this->attributes->addAttribute('aria-valuemax', $this->max);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Bar::withLabel
     *
     * @return static
     */
    public function withLabel()
    {
        $this->withLabel = true;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Bar::striped
     *
     * @return static
     */
    public function striped()
    {
        $this->attributes->addAttributeClass('progress-bar-striped');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Bar::animate
     *
     * @return static
     */
    public function animate()
    {
        $this->attributes->addAttributeClass('active');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Bar::render
     *
     * @return string
     */
    public function render()
    {
        $output[] = $this->open();

        if ($this->withLabel) {
            $output[] = $this->now . '%';
        } else {
            $output[] = $this->label;
        }

        $output[] = $this->close();

        return implode(PHP_EOL, $output);
    }
}