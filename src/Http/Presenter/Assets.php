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

namespace O2System\Framework\Http\Presenter;

// ------------------------------------------------------------------------

use O2System\Spl\Traits\Collectors\FilePathCollectorTrait;

/**
 * Class Assets
 *
 * @package O2System\Framework\Http\Presenter
 */
class Assets
{
    use FilePathCollectorTrait;

    /**
     * Assets::$head
     *
     * @var \O2System\Framework\Http\Presenter\Assets\Positions\Head
     */
    protected $head;

    /**
     * Assets::$body
     *
     * @var \O2System\Framework\Http\Presenter\Assets\Positions\Body
     */
    protected $body;

    // ------------------------------------------------------------------------

    /**
     * Assets::__construct
     */
    public function __construct()
    {
        $this->addFilePaths([
            PATH_RESOURCES,
            PATH_RESOURCES . 'views' . DIRECTORY_SEPARATOR
        ]);

        $this->head = new Assets\Positions\Head();
        $this->body = new Assets\Positions\Body();
    }

    // ------------------------------------------------------------------------

    /**
     * Assets::autoload
     *
     * @param array $assets
     *
     * @return void
     */
    public function autoload($assets)
    {
        foreach ($assets as $position => $collections) {
            if (property_exists($this, $position)) {

                if ($collections instanceof \ArrayObject) {
                    $collections = $collections->getArrayCopy();
                }

                $this->{$position}->loadCollections($collections);
            } elseif ($position === 'packages') {
                $this->loadPackages($collections);
            } elseif ($position === 'css') {
                $this->loadCss($collections);
            } elseif ($position === 'js') {
                $this->loadJs($collections);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Assets::loadPackages
     *
     * @param array $packages
     *
     * @return void
     */
    public function loadPackages($packages)
    {
        foreach ($packages as $package => $files) {
            if (is_string($files)) {
                $this->loadPackage($files);
            } elseif (is_array($files)) {
                $this->loadPackage($package, $files);
            } elseif (is_object($files)) {
                $this->loadPackage($package, get_object_vars($files));
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Assets::loadPackage
     *
     * @param string $package
     * @param array  $subPackages
     *
     * @return void
     */
    public function loadPackage($package, $subPackages = [])
    {
        $packageDir = implode(DIRECTORY_SEPARATOR, [
                'packages',
                $package,
            ]) . DIRECTORY_SEPARATOR;

        if (count($subPackages)) {

            if (array_key_exists('libraries', $subPackages)) {
                foreach ($subPackages[ 'libraries' ] as $subPackageFile) {
                    $pluginDir = $packageDir . 'libraries' . DIRECTORY_SEPARATOR;
                    $pluginName = $subPackageFile;

                    if ($this->body->loadFile($pluginDir . $pluginName . DIRECTORY_SEPARATOR . $pluginName . '.js')) {
                        $this->head->loadFile($pluginDir . $pluginName . DIRECTORY_SEPARATOR . $pluginName . '.css');
                    } else {
                        $this->body->loadFile($pluginDir . $pluginName . '.js');
                    }
                }

                unset($subPackages[ 'libraries' ]);
            }

            $this->head->loadFile($packageDir . $package . '.css');
            $this->body->loadFile($packageDir . $package . '.js');

            foreach ($subPackages as $subPackage => $subPackageFiles) {
                if ($subPackage === 'theme' or $subPackage === 'themes') {
                    if (is_string($subPackageFiles)) {
                        $subPackageFiles = [$subPackageFiles];
                    }

                    foreach ($subPackageFiles as $themeName) {
                        $themeDir = $packageDir . 'themes' . DIRECTORY_SEPARATOR;

                        if ($this->head->loadFile($themeDir . $themeName . DIRECTORY_SEPARATOR . $themeName . '.css')) {
                            $this->body->loadFile($themeDir . $themeName . DIRECTORY_SEPARATOR . $themeName . '.js');
                        } else {
                            $this->head->loadFile($themeDir . $themeName . '.css');
                        }
                    }
                } elseif ($subPackage === 'plugins') {
                    foreach ($subPackageFiles as $subPackageFile) {
                        $pluginDir = $packageDir . 'plugins' . DIRECTORY_SEPARATOR;
                        $pluginName = $subPackageFile;

                        $pluginName = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $pluginName);
                        if (strpos($pluginName, DIRECTORY_SEPARATOR) !== false) {
                            $pluginDir .= pathinfo($pluginName, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR;
                            $pluginName = pathinfo($pluginName, PATHINFO_BASENAME);
                        }

                        if ($this->body->loadFile($pluginDir . $pluginName . DIRECTORY_SEPARATOR . $pluginName . '.js')) {
                            $this->head->loadFile($pluginDir . $pluginName . DIRECTORY_SEPARATOR . $pluginName . '.css');
                        } else {
                            $this->body->loadFile($pluginDir . $pluginName . '.js');
                        }
                    }
                } elseif ($subPackage === 'libraries') {
                    foreach ($subPackageFiles as $subPackageFile) {
                        $libraryDir = $packageDir . 'libraries' . DIRECTORY_SEPARATOR;
                        $libraryName = $subPackageFile;

                        $libraryName = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $libraryName);
                        if (strpos($libraryName, DIRECTORY_SEPARATOR) !== false) {
                            $libraryDir .= pathinfo($libraryName, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR;
                            $libraryName = pathinfo($libraryName, PATHINFO_BASENAME);
                        }

                        if ($this->body->loadFile($libraryDir . $libraryName . DIRECTORY_SEPARATOR . $libraryName . '.js')) {
                            $this->head->loadFile($libraryDir . $libraryName . DIRECTORY_SEPARATOR . $libraryName . '.css');
                        } else {
                            $this->body->loadFile($libraryDir . $libraryName . '.js');
                        }
                    }
                }
            }
        } else {
            $this->head->loadFile($packageDir . $package . '.css');
            $this->body->loadFile($packageDir . $package . '.js');
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Assets::loadCss
     *
     * @param string|array $files
     *
     * @return void
     */
    public function loadCss($files)
    {
        $files = is_string($files) ? [$files] : $files;
        $this->head->loadCollections(['css' => $files]);
    }

    // ------------------------------------------------------------------------

    /**
     * Assets::loadJs
     *
     * @param string|array $files
     * @param string       $position
     *
     * @return void
     */
    public function loadJs($files, $position = 'body')
    {
        $files = is_string($files) ? [$files] : $files;
        $this->{$position}->loadCollections(['js' => $files]);
    }

    // ------------------------------------------------------------------------

    /**
     * Assets::loadFiles
     *
     * @param array $assets
     *
     * @return void
     */
    public function loadFiles($assets)
    {
        foreach ($assets as $type => $item) {
            $addMethod = 'load' . ucfirst($type);

            if (method_exists($this, $addMethod)) {
                call_user_func_array([&$this, $addMethod], [$item]);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Assets::theme
     *
     * @param string $path
     *
     * @return string
     */
    public function theme($path)
    {
        $path = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);

        if (is_file($filePath = PATH_THEME . $path)) {
            return path_to_url($filePath);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Assets::file
     *
     * @param string $file
     *
     * @return string
     */
    public function file($file)
    {
        $filePaths = loader()->getPublicDirs(true);

        foreach ($filePaths as $filePath) {
            if (is_file($filePath . $file)) {
                return path_to_url($filePath . $file);
                break;
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Assets::image
     *
     * @param string $image
     *
     * @return string
     */
    public function image($image)
    {
        $filePaths = loader()->getPublicDirs(true);
        $image = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $image);

        foreach ($filePaths as $filePath) {
            $filePath .= 'img' . DIRECTORY_SEPARATOR;

            if (is_file($filePath . $image)) {
                return path_to_url($filePath . $image);
                break;
            }

            unset($filePath);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Assets::media
     *
     * @param string $media
     *
     * @return string
     */
    public function media($media)
    {
        $filePaths = loader()->getPublicDirs(true);
        $media = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $media);

        foreach ($filePaths as $filePath) {
            $filePath .= 'media' . DIRECTORY_SEPARATOR;

            if (is_file($filePath . $media)) {
                return path_to_url($filePath . $media);
                break;
            }

            unset($filePath);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Assets::parseSourceCode
     *
     * @param string $sourceCode
     *
     * @return string
     */
    public function parseSourceCode($sourceCode)
    {
        $sourceCode = str_replace(
            [
                '"../assets/',
                "'../assets/",
                "(../assets/",
            ],
            [
                '"' . base_url() . '/assets/',
                "'" . base_url() . '/assets/',
                "(" . base_url() . '/assets/',
            ],
            $sourceCode);

        if (presenter()->theme) {
            $sourceCode = str_replace(
                [
                    '"assets/',
                    "'assets/",
                    "(assets/",

                    // with dot
                    '"./assets/',
                    "'./assets/",
                    "(./assets/",
                ],
                [
                    '"' . presenter()->theme->getUrl('assets/'),
                    "'" . presenter()->theme->getUrl('assets/'),
                    "(" . presenter()->theme->getUrl('assets/'),

                    // with dot
                    '"' . presenter()->theme->getUrl('assets/'),
                    "'" . presenter()->theme->getUrl('assets/'),
                    "(" . presenter()->theme->getUrl('assets/'),
                ],
                $sourceCode);
        }

        // Valet path fixes
        if (isset($_SERVER[ 'SCRIPT_FILENAME' ])) {
            $valetPath = dirname($_SERVER[ 'SCRIPT_FILENAME' ]) . DIRECTORY_SEPARATOR;
        } else {
            $PATH_ROOT = $_SERVER[ 'DOCUMENT_ROOT' ];

            if (isset($_SERVER[ 'PHP_SELF' ])) {
                $valetPath = $PATH_ROOT . dirname($_SERVER[ 'PHP_SELF' ]) . DIRECTORY_SEPARATOR;
            } elseif (isset($_SERVER[ 'DOCUMENT_URI' ])) {
                $valetPath = $PATH_ROOT . dirname($_SERVER[ 'DOCUMENT_URI' ]) . DIRECTORY_SEPARATOR;
            } elseif (isset($_SERVER[ 'REQUEST_URI' ])) {
                $valetPath = $PATH_ROOT . dirname($_SERVER[ 'REQUEST_URI' ]) . DIRECTORY_SEPARATOR;
            } elseif (isset($_SERVER[ 'SCRIPT_NAME' ])) {
                $valetPath = $PATH_ROOT . dirname($_SERVER[ 'SCRIPT_NAME' ]) . DIRECTORY_SEPARATOR;
            }
        }

        if (isset($valetPath)) {
            $sourceCode = str_replace($valetPath, '/', $sourceCode);
        }

        return $sourceCode;
    }

    // ------------------------------------------------------------------------

    /**
     * Assets::getHead
     *
     * @return \O2System\Framework\Http\Presenter\Assets\Positions\Head
     */
    public function getHead()
    {
        return $this->head;
    }

    // ------------------------------------------------------------------------

    /**
     * Assets::getBody
     *
     * @return \O2System\Framework\Http\Presenter\Assets\Positions\Body
     */
    public function getBody()
    {
        return $this->body;
    }
}
