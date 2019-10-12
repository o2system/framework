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
 * Class Maintenance
 *
 * @package O2System\Framework\Http\Controllers
 */
class Maintenance extends Controller
{
    /**
     * Maintenance::$inherited
     *
     * Controller inherited flag.
     *
     * @var bool
     */
    static public $inherited = true;

    // ------------------------------------------------------------------------

    /**
     * Maintenance::index
     *
     * @param int $code
     */
    public function index()
    {
        if (presenter()->theme) {
            if (presenter()->theme->hasLayout('maintenance')) {
                presenter()->theme->setLayout('maintenance');
            }

            if (false !== ($layout = presenter()->theme->getLayout())) {
                if ($layout->getFilename() === 'theme') {
                    presenter()->setTheme(false);
                }
            }
        }

        if (cache()->hasItem('maintenance')) {
            $maintenanceInfo = cache()->getItem('maintenance')->get();
            view()->load('maintenance', $maintenanceInfo);
        }
    }
}