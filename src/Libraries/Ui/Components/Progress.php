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

namespace O2System\Framework\Libraries\Ui\Components;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components\Progress\Bar;
use O2System\Framework\Libraries\Ui\Element;
use O2System\Spl\Iterators\ArrayIterator;

/**
 * Class Progress
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class Progress extends Element
{
    /**
     * Progress::$bars
     *
     * @var \O2System\Spl\Iterators\ArrayIterator
     */
    protected $bars;

    // ------------------------------------------------------------------------

    /**
     * Progress::__construct
     *
     * @param int $now
     * @param int $min
     * @param int $max
     */
    public function __construct($now = 0, $min = 0, $max = 100)
    {
        parent::__construct('div');
        $this->attributes->addAttributeClass('progress');

        $this->bars = new ArrayIterator();

        $this->addBar($now, $min, $max);

        $this->setNow($now);
        $this->setMin($min);
        $this->setMax($max);
    }

    // ------------------------------------------------------------------------

    /**
     * Progress::addBar
     *
     * @param int    $now
     * @param int    $min
     * @param int    $max
     * @param string $contextualClass
     *
     * @return static
     */
    public function addBar($now = 0, $min = 0, $max = 100, $contextualClass = 'primary')
    {
        if ($now instanceof Bar) {
            $this->bars->push($now);
        } elseif (is_numeric($now)) {
            $bar = new Bar($now, $min, $max, $contextualClass);
            $this->bars->push($bar);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Progress::setNow
     *
     * @param int $number
     *
     * @return static
     */
    public function setNow($number)
    {
        $this->bars->first()->setNow($number);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Progress::setMin
     *
     * @param int $number
     *
     * @return static
     */
    public function setMin($number)
    {
        $this->bars->first()->setMin($number);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Progress::setMax
     *
     * @param int $number
     *
     * @return static
     */
    public function setMax($number)
    {
        $this->bars->first()->setMax($number);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Progress::withLabel
     *
     * @return static
     */
    public function withLabel()
    {
        $this->bars->first()->withLabel();

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Progress::striped
     *
     * @return static
     */
    public function striped()
    {
        $this->bars->first()->attributes->addAttributeClass('progress-bar-striped');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Progress::animate
     *
     * @return static
     */
    public function animate()
    {
        $firstBar = $this->bars->first();
        $firstBar->attributes->addAttributeClass('active');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Progress::render
     *
     * @return string
     */
    public function render()
    {
        $output[] = $this->open();

        foreach ($this->bars as $bar) {
            $output[] = $bar->render();
        }

        $output[] = $this->close();

        return implode(PHP_EOL, $output);
    }
}