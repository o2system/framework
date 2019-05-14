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
     * Error::$inherited
     *
     * Controller inherited flag.
     *
     * @var bool
     */
    static public $inherited = true;

    // ------------------------------------------------------------------------

    /**
     * Error::index
     *
     * @param int $code
     */
    public function index($code = 500)
    {
        $codeString = $code . '_' . error_code_string($code);

        $viewFilePath = 'error-code';

        if (presenter()->theme) {
            if (presenter()->theme->hasLayout('error-code')) {
                presenter()->theme->setLayout('error-code');
            }

            if (false !== ($layout = presenter()->theme->getLayout())) {
                if ($layout->getFilename() === 'theme') {
                    presenter()->setTheme(false);
                }
            }
        }

        view($viewFilePath, [
            'code'    => $code,
            'title'   => language()->getLine($codeString . '_TITLE'),
            'message' => language()->getLine($codeString . '_MESSAGE'),
        ]);
    }
}