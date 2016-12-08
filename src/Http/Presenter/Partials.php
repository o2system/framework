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

use O2System\Psr\Patterns\AbstractCollectorPattern;

/**
 * Class Partials
 *
 * @package O2System\Framework\Http\View\Presenter
 */
class Partials extends AbstractCollectorPattern
{
    public function __get ( $partial )
    {
        if ( $this->hasItem( $partial ) ) {

            $partialContent = $this->getItem( $partial );

            if ( is_file( $partialContent ) ) {
                parser()->loadFile( $partialContent );

                return parser()->parse();
            } else {
                return $partialContent;
            }
        }

        return null;
    }
}