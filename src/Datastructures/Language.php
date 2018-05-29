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

namespace O2System\Framework\Datastructures;

// ------------------------------------------------------------------------

use O2System\Spl\Datastructures\SplArrayObject;
use O2System\Spl\Info\SplDirectoryInfo;

/**
 * Class Language
 *
 * @package O2System\Framework\Datastructures
 */
class Language extends SplDirectoryInfo
{
    /**
     * Language Properties
     *
     * @var array
     */
    private $properties = [];

    public function __construct($dir)
    {
        parent::__construct($dir);

        // Set Properties
        if (is_file($propertiesFilePath = $dir . DIRECTORY_SEPARATOR . 'language.jsprop')) {
            $properties = json_decode(file_get_contents($propertiesFilePath), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $this->properties = $properties;
            }
        }
    }

    public function isValid()
    {
        if (count($this->properties)) {
            return true;
        }

        return false;
    }

    public function getParameter()
    {
        return $this->getDirName();
    }

    public function getCode()
    {
        return strtoupper(substr(md5($this->getDirName()), 2, 7));
    }

    public function getChecksum()
    {
        return md5($this->getMTime());
    }

    public function getProperties()
    {
        return new SplArrayObject($this->properties);
    }
}