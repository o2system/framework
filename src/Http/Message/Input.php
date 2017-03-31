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

namespace O2System\Framework\Http\Message;

// ------------------------------------------------------------------------

/**
 * Class Input
 *
 * @package O2System\Framework\Http\Message
 */
class Input extends \O2System\Kernel\Http\Input
{
    /**
     * Is Post Insert
     *
     * Is a insert post form request
     *
     * @access  public
     * @return  boolean
     */
    public function isPostInsert()
    {
        if ( empty( $_POST[ 'id' ] ) AND
            (
                isset( $_POST[ 'add' ] ) OR
                isset( $_POST[ 'add_new' ] ) OR
                isset( $_POST[ 'add_as_new' ] ) OR
                isset( $_POST[ 'save' ] )
            )
        ) {
            return true;
        }

        return false;
    }

    //--------------------------------------------------------------------

    /**
     * Is Post Update
     *
     * Is a update post form request
     *
     * @access  public
     * @return  boolean
     */
    public function isPostUpdate()
    {
        if ( ! empty( $_POST[ 'id' ] ) AND
            (
                isset( $_POST[ 'update' ] ) OR
                isset( $_POST[ 'save' ] )
            )
        ) {
            return true;
        }

        return false;
    }
}