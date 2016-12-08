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

namespace O2System\Framework\Http\Presenter\Title;

// ------------------------------------------------------------------------

use O2System\Spl\Datastructures\SplArrayQueue;

/**
 * Class Browser
 *
 * @package O2System\Framework\View\Presenter\Models\Title
 */
class Browser extends SplArrayQueue
{
    /**
     * Title Separator
     *
     * @var string
     */
    private $separator = '&#8212;';

    // ------------------------------------------------------------------------

    /**
     * Set Separator
     *
     * @param string $separator
     *
     * @return Browser
     */
    public function setSeparator ( $separator )
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
    public function __toString ()
    {
        return implode( ' ' . $this->separator . ' ', $this->getArrayCopy() );
    }
}