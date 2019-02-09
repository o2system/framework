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

namespace O2System\Framework\Http\Presenter\Assets\Positions\Abstracts;

// ------------------------------------------------------------------------

use O2System\Kernel\Http\Message\Uri;

/**
 * Class AbstractPosition
 *
 * @package O2System\Framework\Http\Presenter\Assets\Positions\Abstracts
 */
abstract class AbstractPosition
{
    /**
     * AbstractPosition::__get
     *
     * @param string $property
     *
     * @return \O2System\Framework\Http\Presenter\Assets\Collections\Css|\O2System\Framework\Http\Presenter\Assets\Collections\Font|\O2System\Framework\Http\Presenter\Assets\Collections\Javascript|null
     */
    public function __get($property)
    {
        return isset($this->{$property}) ? $this->{$property} : null;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractPosition::getUrl
     *
     * @param string $realPath
     *
     * @return string
     */
    public function getUrl($realPath)
    {
        if (strpos($realPath, 'http') !== false) {
            return $realPath;
        }

        return (new Uri())
            ->withQuery(null)
            ->withSegments(
                new Uri\Segments(
                    str_replace(
                        [PATH_PUBLIC, DIRECTORY_SEPARATOR],
                        ['', '/'],
                        $realPath
                    )
                )
            )
            ->__toString();
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractPosition::loadCollections
     *
     * @param array $collections
     */
    public function loadCollections(array $collections)
    {
        foreach ($collections as $subDir => $files) {
            if (is_array($files) and count($files)) {
                // normalize the subDirectory with a trailing separator
                $subDir = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $subDir);
                $subDir = rtrim($subDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

                foreach ($files as $file) {
                    if (strpos($file, 'http') !== false) {
                        $this->loadUrl($file, $subDir);
                    } else {
                        $this->loadFile($file, $subDir);
                    }
                }
            } elseif (is_string($files)) {
                $this->loadFile($files);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractPosition::loadUrl
     *
     * @param string      $url
     * @param string|null $subDir
     *
     * @return bool
     */
    public function loadUrl($url, $subDir = null)
    {
        $property = is_null($subDir) ? 'css' : null;

        if (is_null($property)) {
            switch ($subDir) {
                default:
                case 'css/':
                    $property = 'css';
                    break;
                case 'font/':
                case 'fonts/':
                    $property = 'font';
                    break;
                case 'js/':
                    $property = 'javascript';
                    break;
            }
        }

        if (property_exists($this, $property)) {
            if ( ! call_user_func_array([$this->{$property}, 'has'], [$url])) {
                $this->{$property}->append($url);

                return true;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractPosition::loadFile
     *
     * @param string $filePath
     * @param string $subDir
     *
     * @return bool
     */
    public function loadFile($filePath, $subDir = null)
    {
        $directories = loader()->getPublicDirs(true);
        $filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);

        // set filepaths
        foreach ($directories as $directory) {
            $extension = pathinfo($directory . $filePath, PATHINFO_EXTENSION);

            if (empty($extension) and empty($subDir)) {
                $extensions = ['.css', '.js'];
            } elseif (empty($extension) and isset($subDir)) {
                switch ($subDir) {
                    default:
                    case 'css/':
                        $property = 'css';
                        $extensions = ['.css'];
                        break;
                    case 'font/':
                    case 'fonts/':
                        $property = 'font';
                        $extensions = ['.css'];
                        break;
                    case 'js/':
                    case 'javascript/':
                    case 'javascripts/':
                        $property = 'javascript';
                        $extensions = ['.js'];
                        break;
                }
            } else {
                // remove filename extension
                $filePath = str_replace('.' . $extension, '', $filePath);
                switch ($extension) {
                    default:
                    case 'css':
                        $property = 'css';
                        $subDir = 'css' . DIRECTORY_SEPARATOR;
                        $extensions = ['.css'];
                        break;
                    case 'font':
                        $property = 'font';
                        $subDir = 'fonts' . DIRECTORY_SEPARATOR;
                        $extensions = ['.css'];
                        break;
                    case 'js':
                    case 'javascript':
                        $property = 'javascript';
                        $subDir = 'js' . DIRECTORY_SEPARATOR;
                        $extensions = ['.js'];
                        break;
                }
            }

            foreach ($extensions as $extension) {
                // without subdirectory
                if (input()->env('DEBUG_STAGE') === 'PRODUCTION') {
                    $filePaths[] = $directory . $filePath . '.min' . $extension; // minify version support
                }

                $filePaths[] = $directory . $filePath . $extension;

                // with subdirectory
                if (isset($subDir)) {
                    if (input()->env('DEBUG_STAGE') === 'PRODUCTION') {
                        $filePaths[] = $directory . $subDir . $filePath . '.min' . $extension; // minify version support
                    }

                    $filePaths[] = $directory . $subDir . $filePath . $extension;
                }
            }
        }

        foreach ($filePaths as $filePath) {
            if (is_file($filePath)) {
                if (empty($property)) {
                    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                    switch ($extension) {
                        case 'font':
                            $extension = 'font';
                            break;
                        case 'js':
                            $extension = 'javascript';
                            break;
                        default:
                        case 'css':
                            $extension = 'css';
                            break;
                    }
                }

                if (property_exists($this, $property)) {
                    if ( ! call_user_func_array([$this->{$property}, 'has'], [$filePath])) {
                        $this->{$property}->append($filePath);

                        return true;
                        break;
                    }
                }
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractPosition::getVersion
     *
     * @param string $code
     *
     * @return string
     */
    public function getVersion($codeSerialize)
    {
        $codeMd5 = md5($codeSerialize);

        $strSplit = str_split($codeMd5, 4);
        foreach ($strSplit as $strPart) {
            $strInt[] = str_pad(hexdec($strPart), 5, '0', STR_PAD_LEFT);
        }

        $codeVersion = round(implode('', $strInt), 10);

        return substr_replace($codeVersion, '.', 3, strlen($codeVersion) - 5);
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractPosition::__toString
     *
     * @return string
     */
    abstract public function __toString();
}