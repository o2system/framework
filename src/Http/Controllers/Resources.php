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

use O2System\Filesystem\Handlers\Downloader;
use O2System\Framework\Http\Controller;
use O2System\Spl\Info\SplFileInfo;

/**
 * Class Resources
 *
 * @package O2System\Framework\Http\Controllers
 */
class Resources extends Storage
{
    /**
     * Resources::$inherited
     *
     * Controller inherited flag.
     *
     * @var bool
     */
    static public $inherited = true;

    /**
     * Resources::$directoryPath
     *
     * @var string
     */
    public $directoryPath;

    /**
     * Resources::$speedLimit
     *
     * @var int
     */
    public $speedLimit = 1024;

    /**
     * Resources::$resumeable
     *
     * @var bool
     */
    public $resumeable = true;

    // ------------------------------------------------------------------------

    /**
     * Resources::__construct
     */
    public function __construct()
    {
        $this->directoryPath = PATH_RESOURCES;
    }
}
