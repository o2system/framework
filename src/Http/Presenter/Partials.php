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

use O2System\Psr\Patterns\AbstractItemStoragePattern;

/**
 * Class Partials
 *
 * @package O2System\Framework\Http\Presenter
 */
class Partials extends AbstractItemStoragePattern
{
    public function hasPartial( $partialOffset )
    {
        return $this->__isset( $partialOffset );
    }

    public function addPartial( $partialOffset, $partialFilePath )
    {
        $this->store( $partialOffset, $partialFilePath );
    }

    public function __get( $partial )
    {
        $partialContent = parent::__get( $partial );

        if ( is_file( $partialContent ) ) {
            parser()->loadFile( $partialContent );

            return parser()->parse();
        } elseif ( is_string( $partialContent ) ) {
            return $partialContent;
        }

        return null;
    }
}