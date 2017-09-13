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

namespace O2System\Framework\Http\Controllers;

// ------------------------------------------------------------------------

use O2System\Framework\Http\Controller;

/**
 * Class Error
 *
 * @package O2System\Framework\Http\Controllers
 */
class Error extends Controller
{
    /**
     * Error::index
     *
     * @param int $code
     */
    public function index( $code = 500 )
    {
        $codeString = $code . error_code_string( $code );

        view( 'error/code', [
            'code'    => $code,
            'title'   => language()->getLine( $codeString . '_TITLE' ),
            'message' => language()->getLine( $codeString . '_MESSAGE' ),
        ] );
    }
}