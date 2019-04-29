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

namespace O2System\Framework\Containers\Modules\DataStructures\Module\Theme\Layout;

// ------------------------------------------------------------------------

use O2System\Spl\Patterns\Structural\Repository\AbstractRepository;
use O2System\Spl\Info\SplFileInfo;

/**
 * Class Partials
 *
 * @package O2System\Framework\Containers\Modules\DataStructures\Module\Theme\Layout
 */
class Partials extends AbstractRepository
{
    /**
     * Partials::$path
     *
     * @var string
     */
    protected $path;

    /**
     * Partials::$extension
     *
     * @var string
     */
    protected $extension;

    // ------------------------------------------------------------------------

    /**
     * Partials::setPath
     *
     * @param string $path
     *
     * @return static
     */
    public function setPath($path)
    {
        if (is_dir($path)) {
            $this->path = $path;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Partials::setExtension
     *
     * @param string $extension
     *
     * @return static
     */
    public function setExtension($extension)
    {
        $this->extension = '.' . rtrim($extension, '.');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Partials::autoload
     */
    public function autoload()
    {
        if (is_dir($this->path)) {
            $this->loadDir($this->path);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Partials::loadDir
     *
     * @param string $dir
     */
    public function loadDir($dir)
    {
        $partialsFiles = scandir($dir);
        $partialsFiles = array_slice($partialsFiles, 2);

        foreach ($partialsFiles as $partialsFile) {

            $partialsFilePath = $dir . $partialsFile;

            if (is_file($partialsFilePath)) {
                $this->loadFile($partialsFilePath);
            } elseif (is_dir($partialsFilePath)) {
                $this->loadDir($partialsFilePath . DIRECTORY_SEPARATOR);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Partials::loadFile
     *
     * @param string $filePath
     */
    public function loadFile($filePath)
    {
        if (strrpos($filePath, $this->extension) !== false) {
            $fileKey = str_replace([$this->path, $this->extension, DIRECTORY_SEPARATOR], ['', '', '-'], $filePath);
            $this->store(camelcase($fileKey), new SplFileInfo($filePath));
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Partials::hasPartial
     *
     * @param string $partialOffset
     *
     * @return bool
     */
    public function hasPartial($partialOffset)
    {
        return $this->__isset($partialOffset);
    }

    // ------------------------------------------------------------------------

    /**
     * Partials::get
     *
     * @param string $partial
     *
     * @return false|mixed|string|null
     */
    public function get($partial)
    {
        $partialContent = parent::get($partial);

        if (is_file($partialContent)) {
            parser()->loadFile($partialContent);

            return parser()->parse();
        } elseif (is_string($partialContent)) {
            return $partialContent;
        }

        return null;
    }
}