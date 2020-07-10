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

namespace O2System\Framework\Containers;

// ------------------------------------------------------------------------

use O2System\Cache\Item;
use O2System\Framework\Containers\Modules\DataStructures;
use O2System\Framework\Services\Hooks;
use O2System\Kernel\Cli\Writers\Format;
use O2System\Kernel\Http\Message\Uri\Segments;
use O2System\Kernel\Http\Router\Addresses;
use O2System\Spl\DataStructures\SplArrayStack;
use O2System\Spl\Info\SplNamespaceInfo;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class Modules
 *
 * @package O2System\Framework\Containers
 */
class Modules extends SplArrayStack
{
    /**
     * Modules::loaded
     *
     * @var array
     */
    private $loaded = [];
    // ------------------------------------------------------------------------

    /**
     * Modules::__construct
     */
    public function __construct()
    {
        parent::__construct();

        // Autoload Main Modularity: Kernel, Framework and App
        $kernel = (new DataStructures\Module(PATH_KERNEL))
            ->setType('KERNEL')
            ->setNamespace('O2System\Kernel\\');
        $this->push($kernel);

        $framework = (new DataStructures\Module(PATH_FRAMEWORK))
            ->setType('FRAMEWORK')
            ->setNamespace('O2System\Framework\\');
        $this->push($framework);
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::autoload
     *
     * @param \O2System\Framework\Containers\Modules\DataStructures\Module $module
     */
    private function autoload(DataStructures\Module $module)
    {
        config()->addFilePath($module->getRealPath());

        if (!in_array($module->getRealPath(), $this->loaded)) {
            // Register to loaded property
            $this->loaded[] = $module->getRealPath();

            // Register Framework\Services\Loader Namespace
            loader()->addNamespace($module->getNamespace(), $module->getRealPath());

            $this->autoloadHelpers($module);

            if (!in_array($module->getType(), ['KERNEL', 'FRAMEWORK'])) {
                // Autoload Module Language
                language()
                    ->addFilePath($module->getRealPath())
                    ->loadFile(dash($module->getParameter()));

                // Autoload Module Config
                $this->autoloadConfig($module);

                // Add View Resource Directory
                if(services()->has('view')) {
                    //view()->addFilePath($module->getResourcesDir());
                    presenter()->assets->pushFilePath($module->getResourcesDir());

                    // Initialize Presenter
                    if($module->getType() === 'APP') {
                        if(config()->offsetExists('view')) {
                            presenter()->setConfig(config('view')->presenter);
                        }

                        // autoload presenter assets
                        if (isset(config('view')->presenter[ 'assets' ])) {
                            presenter()->assets->autoload(config('view')->presenter[ 'assets' ]);
                        }

                        // autoload presenter theme
                        if(isset(config('view')->presenter['theme'])) {
                            presenter()->setTheme($module->getTheme(config('view')->presenter['theme'], false));
                        }
                    }
                }

                // Autoload Module Addresses
                $this->autoloadAddresses($module);

                // Autoload Module Hooks Closures
                $this->autoloadHooks($module);

                // Autoload Module Models
                $this->autoloadModels($module);

                // Autoload Services Services
                $this->autoloadServices($module);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::autoloadHelpers
     *
     * Autoload modules helpers.
     *
     * @param \O2System\Framework\Containers\Modules\DataStructures\Module $module
     */
    private function autoloadHelpers(DataStructures\Module $module)
    {
        loader()->loadHelper(studlycase($module->getParameter()));

        if (is_file(
            $filePath = $module->getRealPath() . 'Config' . DIRECTORY_SEPARATOR . ucfirst(
                    strtolower(ENVIRONMENT)
                ) . DIRECTORY_SEPARATOR . 'Helpers.php'
        )) {
            include($filePath);
        } elseif (is_file($filePath = $module->getRealPath() . 'Config' . DIRECTORY_SEPARATOR . 'Helpers.php')) {
            include($filePath);
        }

        if (isset($helpers) AND is_array($helpers)) {
            loader()->loadHelpers($helpers);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::autoloadConfig
     *
     * Autoload modules config.
     *
     * @param \O2System\Framework\Containers\Modules\DataStructures\Module $module
     */
    private function autoloadConfig(DataStructures\Module $module)
    {
        config()->loadFile('Config');

        $addresses = config()->loadFile('Addresses', true);

        if(empty($addresses)) {
            $controllerNamespace = $module->getNamespace() . 'Controllers\\';
            $controllerClassName = $controllerNamespace . studlycase($module->getParameter());

            $addresses = new Addresses();
            $addresses->any(
                '/',
                function () use ($controllerClassName) {
                    return new $controllerClassName();
                }
            );

            config()->setItem('addresses', $addresses);
        }

        $view = config()->loadFile('View', true);


        config()->reload();
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::autoloadAddresses
     *
     * Autoload modules routes.
     *
     * @param \O2System\Framework\Containers\Modules\DataStructures\Module $module
     */
    private function autoloadAddresses(DataStructures\Module $module)
    {
        // Routes is not available on cli
        if (is_cli()) {
            return;
        }

        if (is_file(
            $filePath = $module->getRealPath() . 'Config' . DIRECTORY_SEPARATOR . ucfirst(
                    strtolower(ENVIRONMENT)
                ) . DIRECTORY_SEPARATOR . 'Addresses.php'
        )) {
            include($filePath);
        } elseif (is_file($filePath = $module->getRealPath() . 'Config' . DIRECTORY_SEPARATOR . 'Addresses.php')) {
            include($filePath);
        }

        if (isset($addresses) AND $addresses instanceof Addresses) {
            config()->addItem('addresses', $addresses);

            unset($addresses);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::autoloadHooks
     *
     * Autoload modules hooks.
     *
     * @param \O2System\Framework\Containers\Modules\DataStructures\Module $module
     */
    private function autoloadHooks(DataStructures\Module $module)
    {
        if (is_file(
            $filePath = $module->getRealPath() . 'Config' . DIRECTORY_SEPARATOR . ucfirst(
                    strtolower(ENVIRONMENT)
                ) . DIRECTORY_SEPARATOR . 'Hooks.php'
        )) {
            include($filePath);
        } elseif (is_file($filePath = $module->getRealPath() . 'Config' . DIRECTORY_SEPARATOR . 'Hooks.php')) {
            include($filePath);
        }

        if (isset($hooks) AND is_array($hooks)) {
            foreach ($hooks as $event => $closures) {
                if ($event === Hooks::PRE_SYSTEM) {
                    // not supported
                    continue;
                }

                if (is_array($closures)) {
                    foreach ($closures as $closure) {
                        hooks()->addClosure($closure, $event);
                    }
                } elseif ($closures instanceof \Closure) {
                    hooks()->addClosure($closures, $event);
                }
            }

            unset($hooks);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::autoloadModels
     *
     * Autoload modules models.
     *
     * @param \O2System\Framework\Containers\Modules\DataStructures\Module $module
     */
    private function autoloadModels(DataStructures\Module $module)
    {
        // Autoload Module Model
        $module->loadModel();
        
        if (is_file(
            $filePath = $module->getRealPath() . 'Config' . DIRECTORY_SEPARATOR . ucfirst(
                    strtolower(ENVIRONMENT)
                ) . DIRECTORY_SEPARATOR . 'Models.php'
        )) {
            include($filePath);
        } elseif (is_file($filePath = $module->getRealPath() . 'Config' . DIRECTORY_SEPARATOR . 'Models.php')) {
            include($filePath);
        }

        if (isset($models) AND is_array($models)) {
            foreach ($models as $offset => $model) {
                if (is_string($model)) {
                    models()->load($model, $offset);
                } elseif (is_object($model)) {
                    models()->add($model);
                }
            }

            unset($models);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::autoloadServices
     *
     * Autoload modules services.
     *
     * @param \O2System\Framework\Containers\Modules\DataStructures\Module $module
     */
    private function autoloadServices(DataStructures\Module $module)
    {
        if (is_file(
            $filePath = $module->getRealPath() . 'Config' . DIRECTORY_SEPARATOR . ucfirst(
                    strtolower(ENVIRONMENT)
                ) . DIRECTORY_SEPARATOR . 'Services.php'
        )) {
            include($filePath);
        } elseif (is_file($filePath = $module->getRealPath() . 'Config' . DIRECTORY_SEPARATOR . 'Services.php')) {
            include($filePath);
        }

        if (isset($services) AND is_array($services)) {
            foreach ($services as $offset => $service) {
                if (is_string($service)) {
                    services()->load($service, $offset);
                } elseif (is_object($service)) {
                    services()->add($service);
                }
            }

            unset($services);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::push
     *
     * Replacement of SplArrayStack push method.
     *
     * @param mixed $module
     */
    public function push($module)
    {
        if ($module instanceof DataStructures\Module) {
            $this->autoload($module);
            parent::push($module);

            if ($module->getType() === 'APP') {
                globals()->offsetSet('app', $module);
            } elseif ($module->getType() === 'MODULE') {
                globals()->offsetSet('module', $module);
            } elseif ($module->getType() === 'PLUGIN') {
                globals()->offsetSet('plugin', $module);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::register
     *
     * @param DataStructures\Module $module
     */
    public function register(DataStructures\Module $module)
    {
        if(in_array($module->getRealPath(), $this->loaded)) {
            return false;
        }

        $this->push($module);
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::find
     *
     * @param mixed $segments
     * @param bool  $register
     *
     * @return bool|DataStructures\Module
     */
    public function find($segments, $register = false)
    {
        if (is_string($segments)) {
            if (strpos($segments, '/') !== false) {
                $segments = explode('/', $segments);
            } else {
                $segments = [$segments];
            }
        } elseif($segments instanceof Segments) {
            $segments = $segments->getArrayCopy();
        }

        if (is_array($segments)) {
            $segments = array_values($segments);

            if(is_dir($packageRealPath = PATH_APP . studlycase($segments[0]) . DIRECTORY_SEPARATOR)) {
                $packageSubDirectories = [
                    'Modules',
                    'Plugins',
                ];
                $packageJsonFile = 'app.json';
            } elseif(is_dir($packageRealPath = PATH_APP . 'Modules' . DIRECTORY_SEPARATOR . studlycase($segments[0]) . DIRECTORY_SEPARATOR)) {
                $packageSubDirectories = [
                    'Plugins',
                ];
                $packageJsonFile = 'module.json';
            } elseif(globals()->offsetExists('app')) {
                if(is_dir($packageRealPath = globals()->app->getRealPath() . studlycase($segments[0]) . DIRECTORY_SEPARATOR)) {
                    $packageSubDirectories = [
                        'Modules',
                        'Plugins',
                    ];
                    $packageJsonFile = 'app.json';
                } elseif(is_dir($packageRealPath = globals()->app->getRealPath() . 'Modules' . DIRECTORY_SEPARATOR . studlycase($segments[0]) . DIRECTORY_SEPARATOR)) {
                    $packageSubDirectories = [
                        'Plugins',
                    ];
                    $packageJsonFile = 'module.json';
                }
            }

            if (is_file($packageJsonFilePath = $packageRealPath . $packageJsonFile)) {
                array_shift($segments);

                if ($packageRegistry = $this->getPackageRegistry($packageJsonFilePath)) {
                    if($register === true) {
                        $this->register($packageRegistry);
                    }
                }
            }

            if(isset($packageSubDirectories)) {
                $segments = array_values($segments);

                $numOfSegments = count($segments);
                $numOfPackageSubDirectories = count($packageSubDirectories);

                if($numOfSegments == 0) {
                    if (is_file($packageJsonFilePath = $packageRealPath . $packageJsonFile)) {
                        if ($packageRegistry = $this->getPackageRegistry($packageJsonFilePath)) {
                            if($register === true) {
                                $this->register($packageRegistry);
                            }

                            return is_null($packageRegistry) ? false : $packageRegistry;
                        }
                    }
                } else {
                    for ($i = 0; $i < $numOfPackageSubDirectories; $i++) {
                        if ($i == $numOfSegments) {
                            break;
                        }

                        $packageDirectory = studlycase($segments[$i]);
                        $packageJsonFile = strtolower(singular($packageSubDirectories[$i])) . '.json';
                        $packageJsonFilePath = $packageRealPath . $packageSubDirectories[$i] . DIRECTORY_SEPARATOR . $packageDirectory . DIRECTORY_SEPARATOR . $packageJsonFile;

                        if (is_file($packageJsonFilePath)) {
                            $packageRealPath = dirname($packageJsonFilePath) . DIRECTORY_SEPARATOR;

                            if ($packageRegistry = $this->getPackageRegistry($packageJsonFilePath)) {
                                if($register === true) {
                                    $this->register($packageRegistry);
                                }
                            }
                        }
                    }

                    return is_null($packageRegistry) ? false : $packageRegistry;
                }
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::loadPackageRegistry
     *
     * @param $packageJsonFile
     * @return DataStructures\Module|bool Returns FALSE if failed.
     */
    private function getPackageRegistry($packageJsonFile)
    {
        $packageJsonFile = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $packageJsonFile);
        $packageJsonFileInfo = pathinfo($packageJsonFile);
        $packageJsonMetadata = json_decode(file_get_contents($packageJsonFile), true);

        if (!is_array($packageJsonMetadata)) {
            return false;
        }

        $modularType = strtoupper($packageJsonFileInfo['filename']);


        $moduleSegments = explode(
            DIRECTORY_SEPARATOR,
            trim(
                str_replace(
                    [
                        PATH_FRAMEWORK,
                        PATH_PUBLIC,
                        PATH_RESOURCES,
                        PATH_APP,
                        $packageJsonFileInfo['basename'],
                    ],
                    '',
                    $packageJsonFile
                ),
                DIRECTORY_SEPARATOR
            )
        );

        $moduleSegments = array_map(function ($string) {
            return dash(snakecase($string));
        }, $moduleSegments);

        $moduleParentSegments = $moduleSegments = array_diff($moduleSegments, [
            'modules',
            'plugins',
            'widgets'
        ]);

        array_pop($moduleParentSegments);

        $moduleNamespace = prepare_namespace(
            str_replace(
                PATH_ROOT,
                '',
                $packageJsonFileInfo['dirname']
            ),
            false
        );

        return (new DataStructures\Module(
            $packageJsonFileInfo['dirname']
        ))
            ->setType($packageJsonFileInfo['filename'])
            ->setNamespace($moduleNamespace)
            ->setSegments($moduleSegments)
            ->setParentSegments($moduleParentSegments)
            ->setProperties($packageJsonMetadata);
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::exists
     *
     * @param string|array $segments
     *
     * @return bool
     */
    public function exists($segments)
    {
        $segments = is_array($segments) ? implode('/', $segments) : $segments;

        if (is_string($segments)) {
            return (bool)array_key_exists($segments, $this->registry);
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::getNamespaces
     *
     * Gets module namespaces.
     *
     * @return array
     */
    public function getNamespaces()
    {
        $namespaces = [];

        foreach ($this as $key => $module) {
            if ($module instanceof DataStructures\Module) {
                $namespaces[$key] = new SplNamespaceInfo($module->getNamespace(), $module->getRealPath());
            }
        }

        return $namespaces;
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::getDirs
     *
     * Gets module directories
     *
     * @param string $subDir
     * @param bool $reverse
     *
     * @return array
     */
    public function getDirs($subDir, $reverse = false)
    {
        $dirs = [];
        $subDir = prepare_class_name($subDir);
        $subDir = str_replace(
            ['\\', '/'],
            DIRECTORY_SEPARATOR,
            $subDir
        );

        foreach ($this as $module) {
            if ($module instanceof DataStructures\Module) {
                if (is_dir($dirPath = $module->getRealPath() . $subDir)) {
                    $dirs[] = $dirPath . DIRECTORY_SEPARATOR;
                }

                if (is_dir($dirPath = $module->getRealPath() . (is_cli() ? 'Cli' : 'Http') . DIRECTORY_SEPARATOR . $subDir)) {
                    $dirs[] = $dirPath . DIRECTORY_SEPARATOR;
                }
            }
        }

        return $reverse === true ? array_reverse($dirs) : $dirs;
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::getResourcesDirs
     *
     * Gets module resources directories
     *
     * @param string $subDir
     * @param bool $reverse
     *
     * @return array
     */
    public function getResourcesDirs($subDir, $reverse = false)
    {
        $dirs = [];
        $subDir = dash($subDir);
        $subDir = str_replace(
            ['\\', '/'],
            DIRECTORY_SEPARATOR,
            $subDir
        );

        foreach ($this as $module) {
            if ($module instanceof DataStructures\Module) {
                if (is_dir($dirPath = $module->getResourcesDir($subDir))) {
                    $dirs[] = $dirPath;
                }
            }
        }

        return $reverse === true ? array_reverse($dirs) : $dirs;
    }
}