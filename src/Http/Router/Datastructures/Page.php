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

namespace O2System\Framework\Http\Router\Datastructures;

// ------------------------------------------------------------------------

use O2System\Spl\Datastructures\SplArrayObject;
use O2System\Spl\Info\SplFileInfo;

/**
 * Class Page
 *
 * @package O2System\Framework\Http\Router\Datastructures
 */
class Page extends SplFileInfo
{
    /**
     * Page Variables
     *
     * @var SplArrayObject
     */
    private $vars = [];

    /**
     * Page Settings
     *
     * @var SplArrayObject
     */
    private $settings;

    // ------------------------------------------------------------------------

    /**
     * Page::__construct
     *
     * @param string $filename
     */
    public function __construct($filename)
    {
        parent::__construct($filename);

        if (file_exists(
            $propsFilePath = $this->getPath() . DIRECTORY_SEPARATOR . str_replace(
                    '.phtml',
                    '.jspage',
                    strtolower($this->getBasename())
                )
        )) {
            $props = file_get_contents($propsFilePath);
            $props = json_decode($props, true);

            if (isset($props[ 'vars' ])) {
                $this->vars = $props[ 'vars' ];
            }

            if (isset($props[ 'settings' ])) {
                $this->settings = new SplArrayObject($props[ 'settings' ]);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Page::getVars
     *
     * Gets page variables.
     *
     * @return \O2System\Spl\Datastructures\SplArrayObject
     */
    public function getVars()
    {
        return $this->vars;
    }

    // ------------------------------------------------------------------------

    /**
     * Page::getSettings
     *
     * Gets page settings.
     *
     * @return bool|\O2System\Spl\Datastructures\SplArrayObject
     */
    public function getSettings()
    {
        if ($this->settings instanceof SplArrayObject) {
            return $this->settings;
        }

        return false;
    }
}