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
     * Page Presets
     *
     * @var SplArrayObject
     */
    private $presets;

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
            $propertiesFilePath = $this->getPath() . DIRECTORY_SEPARATOR . str_replace(
                    '.phtml',
                    '.json',
                    strtolower($this->getBasename())
                )
        )) {
            $properties = file_get_contents($propertiesFilePath);
            $properties = json_decode($properties, true);

            if (isset($properties[ 'vars' ])) {
                $this->vars = $properties[ 'vars' ];
            }

            if (isset($properties[ 'presets' ])) {
                $this->presets = new SplArrayObject($properties[ 'presets' ]);
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
     * Page::getPresets
     *
     * Gets page presets.
     *
     * @return bool|\O2System\Spl\Datastructures\SplArrayObject
     */
    public function getPresets()
    {
        if ($this->presets instanceof SplArrayObject) {
            return $this->presets;
        }

        return false;
    }
}