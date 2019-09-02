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

use MatthiasMullie\Minify\CSS;
use MatthiasMullie\Minify\JS;
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
     * @param string      $filename
     * @param string|null $subDir
     *
     * @return void
     */
    abstract public function loadFile($filename, $subDir = null);

    // ------------------------------------------------------------------------

    /**
     * AbstractPosition::publishFile
     *
     * @param $filePath
     *
     * @return array
     */
    protected function publishFile($filePath)
    {
        $publicFilePath = str_replace(PATH_RESOURCES, PATH_PUBLIC, $filePath);
        $publicFileDir = dirname($publicFilePath) . DIRECTORY_SEPARATOR;

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        $publicMinifyFilePath = str_replace('.' . $extension, '.min.' . $extension, $publicFilePath);

        $fileContent = file_get_contents($filePath);
        $fileVersion = $this->getVersion($fileContent);

        if (is_file($mapFilePath = $publicFilePath . '.map')) {
            $mapMetadata = json_decode(file_get_contents($mapFilePath), true);
            // if the file version is changed delete it first
            if ( ! hash_equals($fileVersion, $mapMetadata[ 'version' ])) {
                unlink($publicFilePath);
                unlink($publicMinifyFilePath);
                unlink($mapFilePath);
            }
        }

        if ( ! is_file($mapFilePath)) {
            if ( ! empty($fileContent)) {
                $mapMetadata = [
                    'version'        => $fileVersion,
                    'sources'        => [
                        str_replace([
                            PATH_ROOT,
                            '\\',
                            '/',
                            DIRECTORY_SEPARATOR,
                        ], [
                            '',
                            '/',
                            '/',
                            '/',
                        ], $filePath),
                    ],
                    'names'          => [],
                    'mappings'       => [],
                    'file'           => pathinfo($publicMinifyFilePath, PATHINFO_BASENAME),
                    'sourcesContent' => [
                        $fileContent,
                    ],
                    'sourceRoot'     => '',
                ];

                if ( ! is_dir($publicFileDir)) {
                    @mkdir($publicFileDir, 0777, true);
                }

                if (is_writable($publicFileDir)) {
                    if ($fileStream = @fopen($publicFilePath, 'ab')) {
                        flock($fileStream, LOCK_EX);
                        fwrite($fileStream, $fileContent);
                        flock($fileStream, LOCK_UN);
                        fclose($fileStream);

                        // File Map
                        if ($fileStream = @fopen($mapFilePath, 'ab')) {
                            flock($fileStream, LOCK_EX);

                            fwrite($fileStream, json_encode($mapMetadata));

                            flock($fileStream, LOCK_UN);
                            fclose($fileStream);
                        }

                        switch ($extension) {
                            case 'min.css':
                            case 'css':
                                $minifyStyleHandler = new CSS($publicFilePath);
                                $minifyStyleHandler->minify($publicMinifyFilePath);
                                break;

                            case 'min.js':
                            case 'js':
                                $minifyJavascriptHandler = new JS($publicFilePath);
                                $minifyJavascriptHandler->minify($publicMinifyFilePath);
                                break;
                        }

                    }
                }
            }
        }

        return [
            'filePath' => $publicFilePath,
            'url'      => $this->getUrl($publicFilePath),
            'minify'   => [
                'filePath' => $publicMinifyFilePath,
                'url'      => $this->getUrl($publicMinifyFilePath),
            ],
            'version'  => $fileVersion,
        ];
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractPosition::bundleFile
     *
     * @param string $filename
     * @param array  $sources
     *
     * @return array
     */
    protected function bundleFile($filename, array $sources)
    {
        $sourcesContent = [];
        foreach ($sources as $key => $source) {
            $content = file_get_contents($source);

            if ( ! empty($content)) {
                $sourcesContent[] = $content;
            } else {
                unset($sources[ $key ]);
            }
        }

        $fileContent = implode(PHP_EOL, $sourcesContent);
        $fileVersion = $this->getVersion($fileContent);

        $publicFilePath = PATH_PUBLIC . $filename;
        $filename = pathinfo($publicFilePath, PATHINFO_BASENAME);

        $publicFileDir = dirname($publicFilePath) . DIRECTORY_SEPARATOR . 'bundled' . DIRECTORY_SEPARATOR;
        $publicFilePath = $publicFileDir . $filename;

        $extension = pathinfo($publicFilePath, PATHINFO_EXTENSION);

        $publicMinifyFilePath = str_replace('.' . $extension, '.min.' . $extension, $publicFilePath);

        if ( ! empty($sourcesContent)) {
            if (is_file($mapFilePath = $publicFilePath . '.map')) {
                $mapMetadata = json_decode(file_get_contents($mapFilePath), true);
                // if the file version is changed delete it first
                if ( ! hash_equals($fileVersion, $mapMetadata[ 'version' ])) {
                    unlink($publicFilePath);
                    unlink($publicMinifyFilePath);
                    unlink($mapFilePath);
                }
            }

            if ( ! is_file($mapFilePath)) {
                if ( ! empty($fileContent)) {
                    $mapMetadata = [
                        'version'        => $fileVersion,
                        'sources'        => $sources,
                        'names'          => [],
                        'mappings'       => [],
                        'file'           => pathinfo($publicMinifyFilePath, PATHINFO_BASENAME),
                        'sourcesContent' => $sourcesContent,
                        'sourceRoot'     => '',
                    ];

                    if ( ! is_writable($publicFileDir)) {
                        @mkdir($publicFileDir, 0777, true);
                    }

                    if (is_writable($publicFileDir)) {
                        if ($fileStream = @fopen($publicFilePath, 'ab')) {
                            flock($fileStream, LOCK_EX);
                            fwrite($fileStream, $fileContent);
                            flock($fileStream, LOCK_UN);
                            fclose($fileStream);

                            // File Map
                            if ($fileStream = @fopen($mapFilePath, 'ab')) {
                                flock($fileStream, LOCK_EX);

                                fwrite($fileStream, json_encode($mapMetadata));

                                flock($fileStream, LOCK_UN);
                                fclose($fileStream);
                            }

                            switch ($extension) {
                                case 'min.css':
                                case 'css':
                                    $minifyStyleHandler = new CSS($publicFilePath);
                                    $minifyStyleHandler->minify($publicMinifyFilePath);
                                    break;

                                case 'min.js':
                                case 'js':
                                    $minifyJavascriptHandler = new JS($publicFilePath);
                                    $minifyJavascriptHandler->minify($publicMinifyFilePath);
                                    break;
                            }

                        }
                    }
                }
            }
        }

        return [
            'filePath' => $publicFilePath,
            'url'      => $this->getUrl($publicFilePath),
            'minify'   => [
                'filePath' => $publicMinifyFilePath,
                'url'      => $this->getUrl($publicMinifyFilePath),
            ],
            'version'  => $fileVersion,
        ];
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractPosition::getFilePath
     *
     * @param string      $filename
     * @param string|null $subDir
     *
     * @return string
     */
    protected function getFilePath($filename, $subDir = null)
    {
        $directories = presenter()->assets->getFilePaths();

        foreach ($directories as $directory) {
            if (strpos($filename, 'app') !== false) {
                if ($app = modules()->getActiveApp()) {
                    $directory = $app->getResourcesDir();
                }
            }

            /**
             * Try with sub directory
             * find from public directory first then resource directory
             */
            if (isset($subDir)) {
                $subDir = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $subDir);

                if (is_file($filePath = str_replace(PATH_RESOURCES, PATH_PUBLIC,
                        $directory) . $subDir . $filename)) {
                    return $filePath;
                    break;
                } elseif (is_file($filePath = str_replace(PATH_RESOURCES, PATH_PUBLIC . 'assets' . DIRECTORY_SEPARATOR,
                        $directory) . $subDir . $filename)) {
                    return $filePath;
                    break;
                } elseif (is_file($filePath = $directory . $subDir . $filename)) {
                    return $filePath;
                    break;
                }
            }

            /**
             * Try without sub directory
             * find from public directory first then resource directory
             */
            if (is_file($filePath = str_replace(PATH_RESOURCES, PATH_PUBLIC, $directory) . $filename)) {
                return $filePath;
                break;
            } elseif (is_file($filePath = str_replace(PATH_RESOURCES, PATH_PUBLIC . 'assets' . DIRECTORY_SEPARATOR,
                    $directory) . $filename)) {
                return $filePath;
                break;
            } elseif (is_file($filePath = $directory . $filename)) {
                return $filePath;
                break;
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