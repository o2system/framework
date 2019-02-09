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

namespace O2System\Framework\DataStructures;

// ------------------------------------------------------------------------

use O2System\Spl\DataStructures\SplArrayObject;
use O2System\Spl\Info\SplDirectoryInfo;

/**
 * Class Language
 *
 * @package O2System\Framework\DataStructures
 */
class Language extends SplDirectoryInfo
{
    /**
     * Language Properties
     *
     * @var array
     */
    private $properties = [];

    // ------------------------------------------------------------------------

    /**
     * Language::__construct
     *
     * @param string $dir
     */
    public function __construct($dir)
    {
        parent::__construct($dir);

        // Set Properties
        if (is_file($propertiesFilePath = $dir . DIRECTORY_SEPARATOR . 'language.json')) {
            $properties = json_decode(file_get_contents($propertiesFilePath), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $this->properties = $properties;
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Language::isValid
     *
     * @return bool
     */
    public function isValid()
    {
        if (count($this->properties)) {
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Language::getCode
     *
     * @return string
     */
    public function getCode()
    {
        return strtoupper(substr(md5($this->getDirName()), 2, 7));
    }

    // ------------------------------------------------------------------------

    /**
     * Language::getChecksum
     *
     * @return string
     */
    public function getChecksum()
    {
        return md5($this->getMTime());
    }

    // ------------------------------------------------------------------------

    /**
     * Language::getProperties
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject
     */
    public function getProperties()
    {
        return new SplArrayObject($this->properties);
    }

    // ------------------------------------------------------------------------

    /**
     * Language::getLocale
     *
     * @return mixed
     */
    public function getLocale()
    {
        $parts = explode('-', $this->getParameter());

        return reset($parts);
    }

    // ------------------------------------------------------------------------

    /**
     * Language::getParameter
     *
     * @return string
     */
    public function getParameter()
    {
        return $this->getDirName();
    }

    // ------------------------------------------------------------------------

    /**
     * Language::getIdeom
     *
     * @return mixed
     */
    public function getIdeom()
    {
        $parts = explode('-', $this->getParameter());

        return end($parts);
    }
}