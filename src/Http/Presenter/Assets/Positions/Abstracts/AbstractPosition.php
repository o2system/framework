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
    public function __get($property)
    {
        return isset($this->{$property}) ? $this->{$property} : null;
    }

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
                ->__toString() . '?v=' . filemtime($realPath);
    }

    public function loadCollections(array $collections)
    {
        foreach ($collections as $subDirectory => $files) {
            if (is_array($files)) {
                // normalize the subDirectory with a trailing separator
                $subDirectory = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $subDirectory);
                $subDirectory = rtrim($subDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

                foreach ($files as $file) {
                    if ($subDirectory === 'fonts' . DIRECTORY_SEPARATOR) {
                        $file = $file . DIRECTORY_SEPARATOR . $file;
                        $file = str_replace('.css', '', $file) . '.css';
                    } elseif ($subDirectory === 'css' . DIRECTORY_SEPARATOR) {
                        $file = str_replace('.css', '', $file) . '.css';
                    } elseif ($subDirectory === 'js' . DIRECTORY_SEPARATOR) {
                        $file = str_replace('.js', '', $file) . '.js';
                    }

                    if (strpos($file, 'http') !== false) {
                        $this->loadUrl($file, $subDirectory);
                    } else {
                        $this->loadFile($subDirectory . $file);
                    }
                }
            } elseif (is_string($files)) {
                $this->loadFile($files);
            }
        }
    }

    public function loadUrl($url, $subdirectory)
    {
        if (strpos($url, 'fonts')) {
            $subdirectory = 'font';
        }

        switch ($subdirectory) {
            case 'font':
                $subdirectory = 'font';
                break;
            case 'js':
                $subdirectory = 'javascript';
                break;
            default:
            case 'css':
                $subdirectory = 'css';
                break;
        }

        if (property_exists($this, $subdirectory)) {
            $this->{$subdirectory}->append($url);

            return true;
        }
    }

    public function loadFile($filePath)
    {
        $directories = loader()->getPublicDirs(true);
        $filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);

        // set filepaths
        foreach ($directories as $directory) {
            if (strpos($directory, 'assets') === false) {
                $directory = $directory . 'assets' . DIRECTORY_SEPARATOR;
            }

            $extension = pathinfo($directory . $filePath, PATHINFO_EXTENSION);

            $filePaths[] = $directory . str_replace('.' . $extension, '.min.' . $extension,
                    $filePath); // minify version support
            $filePaths[] = $directory . $filePath;
        }

        foreach ($filePaths as $filePath) {
            if (is_file($filePath)) {

                if (strpos($filePath, 'fonts')) {
                    $extension = 'font';
                }

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

                if (property_exists($this, $extension)) {

                    if ( ! $this->{$extension}->has($filePath)) {
                        $this->{$extension}->append($filePath);

                        return true;
                        break;
                        break;
                    }
                }
            }
        }

        return false;
    }

    abstract public function __toString();
}