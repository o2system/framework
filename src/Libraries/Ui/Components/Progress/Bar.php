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

    protected $label;
    protected $now = 0;
    protected $min = 0;
    protected $max = 100;

    protected $withLabel = false;

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

    public function setMin($number)
    {
        $this->min = (int)$number;
        $this->attributes->addAttribute('aria-valuemin', $this->min);

        return $this;
    }

    public function setMax($number)
    {
        $this->max = (int)$number;
        $this->attributes->addAttribute('aria-valuemax', $this->max);

        return $this;
    }

    public function withLabel()
    {
        $this->withLabel = true;

        return $this;
    }

    public function striped()
    {
        $this->attributes->addAttributeClass('progress-bar-striped');

        return $this;
    }

    public function animate()
    {
        $this->attributes->addAttributeClass('active');

        return $this;
    }

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