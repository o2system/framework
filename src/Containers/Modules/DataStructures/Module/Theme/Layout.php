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

namespace O2System\Framework\Containers\Modules\DataStructures\Module\Theme;

// ------------------------------------------------------------------------

use O2System\Framework\Containers\Modules\DataStructures\Module\Theme\Layout\Partials;
use O2System\Spl\Info\SplFileInfo;

/**
 * Class Layout
 *
 * @package O2System\Framework\Containers\Modules\DataStructures\Module\Theme
 */
class Layout extends SplFileInfo
{
    /**
     * Layout::$partials
     *
     * @var \O2System\Framework\Containers\Modules\DataStructures\Module\Theme\Layout\Partials
     */
    protected $partials;

    // ------------------------------------------------------------------------

    /**
     * Layout::__construct
     *
     * @param string $filePath
     */
    public function __construct($filePath)
    {
        parent::__construct($filePath);

        $this->partials = new Partials();

        $filenameParts = explode('.', pathinfo($filePath, PATHINFO_BASENAME));
        array_shift($filenameParts);

        $this->partials
            ->setPath($this->getPath() . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR)
            ->setExtension(implode('.', $filenameParts))
            ->autoload();
    }

    // ------------------------------------------------------------------------

    /**
     * Layout::getPartials
     *
     * @return \O2System\Framework\Containers\Modules\DataStructures\Module\Theme\Layout\Partials
     */
    public function getPartials()
    {
        return $this->partials;
    }

    // ------------------------------------------------------------------------

    /**
     * Layout::getContents
     *
     * @return false|string
     */
    public function getContents()
    {
        return file_get_contents($this->getRealPath());
    }
}