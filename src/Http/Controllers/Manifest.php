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
 * Class Manifest
 * @package O2System\Framework\Http\Controllers
 */
class Manifest extends Controller
{
    /**
     * Manifest::index
     */
    public function index()
    {
        if(false !== ($config = $this->config->loadFile('manifest', true))) {
            print_out('test');
        }

        print_out($config);
        $this->output->sendPayload($this->presenter->manifest->getArrayCopy());
    }
}