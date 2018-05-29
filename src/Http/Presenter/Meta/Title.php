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

namespace O2System\Framework\Http\Presenter\Meta;

// ------------------------------------------------------------------------

use O2System\Spl\Datastructures\SplArrayQueue;

/**
 * Class Title
 *
 * @package O2System\Framework\Http\Presenter\Meta
 */
class Title extends SplArrayQueue
{
    /**
     * Title Separator
     *
     * @var string
     */
    private $separator = '-';

    // ------------------------------------------------------------------------

    public function append($header)
    {
        $this[] = $header;
    }

    public function replace($header)
    {
        foreach ($this as $index => $string) {
            $this->dequeue();
        }

        $this->prepend($header);
    }

    public function prepend($header)
    {
        $this->add(0, $header);
    }

    /**
     * Set Separator
     *
     * @param string $separator
     *
     * @return static
     */
    public function setSeparator($separator)
    {
        $this->separator = $separator;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Magic Method __toString
     *
     * Convert this class into string
     *
     * @return string
     */
    public function __toString()
    {
        return implode(' ' . $this->separator . ' ', array_unique($this->getArrayCopy()));
    }
}