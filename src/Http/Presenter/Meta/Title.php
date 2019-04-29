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

namespace O2System\Framework\Http\Presenter\Meta;

// ------------------------------------------------------------------------

use O2System\Spl\DataStructures\SplArrayQueue;

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

    /**
     * Title::append
     *
     * @param string $header
     *
     * @return static
     */
    public function append($header)
    {
        $this[] = $header;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Title::replace
     *
     * @param string $header
     *
     * @return static
     */
    public function replace($header)
    {
        foreach ($this as $index => $string) {
            $this->dequeue();
        }

        return $this->prepend($header);
    }

    // ------------------------------------------------------------------------

    /**
     * Title::prepend
     *
     * @param string $header
     *
     * @return static
     */
    public function prepend($header)
    {
        $this->add(0, $header);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Title::setSeparator
     *
     * Sets title separator.
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
     * Title::__toString
     *
     * Magic Method __toString
     *
     * Convert this class into a string
     *
     * @return string
     */
    public function __toString()
    {
        return implode(' ' . $this->separator . ' ', array_unique($this->getArrayCopy()));
    }
}