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
     * Modules::$types
     *
     * List of module types.
     *
     * @var array
     */
    private $types = [
        'apps',
        'modules',
        'components',
        'plugins',
    ];

    /**
     * Modules::$registry
     *
     * Modules registries.
     *
     * @var array
     */
    private $registry = [];

    /**
     * Modules::loaded
     *
     * @var array
     */
    private $loaded = [];

    /**
     * Modules::$activeApp
     *
     * @var \O2System\Framework\Containers\Modules\DataStructures\Module
     */
    private $activeApp;

    /**
     * Modules::$activeModule
     *
     * @var \O2System\Framework\Containers\Modules\DataStructures\Module
     */
    private $activeModule;

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

        $this->autoload($kernel);
        parent::push($kernel);

        $framework = (new DataStructures\Module(PATH_FRAMEWORK))
            ->setType('FRAMEWORK')
            ->setNamespace('O2System\Framework\\');

        $this->autoload($framework);
        parent::push($framework);

        $app = (new DataStructures\Module(PATH_APP))
            ->setType('APP')
            ->setNamespace('App\\');
        $this->activeApp = $app;

        $this->autoload($app);
        parent::push($app);
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::autoload
     *
     * @param \O2System\Framework\Containers\Modules\DataStructures\Module $module
     */
    private function autoload(DataStructures\Module $module)
    {
        if ( ! in_array($module->getRealPath(), $this->loaded)) {
            // Register to loaded property
            $this->loaded[] = $module->getRealPath();

            // Register Framework\Services\Loader Namespace
            loader()->addNamespace($module->getNamespace(), $module->getRealPath());

            $this->autoloadHelpers($module);

            if ( ! in_array($module->getType(), ['KERNEL', 'FRAMEWORK'])) {

                // Autoload Module Language
                language()
                    ->addFilePath($module->getRealPath())
                    ->loadFile(dash($module->getParameter()));

                // Autoload Module Config
                $this->autoloadConfig($module);

                // Autoload Module Addresses
                $this->autoloadAddresses($module);

                // Autoload Module Hooks Closures
                $this->autoloadHooks($module);

                // Autoload Module Models
                $this->autoloadModels($module);

                // Autoload Services Models
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
        if (is_file(
            $filePath = $module->getRealPath() . 'Config' . DIRECTORY_SEPARATOR . ucfirst(
                    strtolower(ENVIRONMENT)
                ) . DIRECTORY_SEPARATOR . 'Config.php'
        )) {
            include($filePath);
        } elseif (is_file($filePath = $module->getRealPath() . 'Config' . DIRECTORY_SEPARATOR . 'Config.php')) {
            include($filePath);
        }

        if (isset($config) AND is_array($config)) {
            // Set default timezone
            if (isset($config[ 'datetime' ][ 'timezone' ])) {
                date_default_timezone_set($config[ 'datetime' ][ 'timezone' ]);
            }

            // Setup Language Ideom and Locale
            if (isset($config[ 'language' ])) {
                language()->setDefault($config[ 'language' ]);
            }

            config()->merge($config);

            unset($config);
        }
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
        if ( ! in_array($module->getNamespace(), ['O2System\Kernel\\', 'O2System\Framework\\', 'App\\'])) {
            if($module instanceof DataStructures\Module) {
                $this->autoload($module);

                parent::push($module);

                if($module->getType() === 'APP') {
                    $this->setActiveApp($module);
                } elseif(in_array($module->getType(), ['MODULE', 'COMPONENT'])) {
                    $this->setActiveModule($module);
                }
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::loadRegistry
     *
     * Load modules registry
     *
     * @return void
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function loadRegistry()
    {
        if (empty($this->registry)) {
            $cacheItemPool = cache()->getItemPool('default');

            if (cache()->hasItemPool('registry')) {
                $cacheItemPool = cache()->getItemPool('registry');
            }

            if ($cacheItemPool instanceof CacheItemPoolInterface) {
                if ($cacheItemPool->hasItem('o2modules')) {
                    if ($registry = $cacheItemPool->getItem('o2modules')->get()) {
                        $this->registry = $registry;
                    } else {
                        $this->registry = $this->fetchRegistry();
                        $cacheItemPool->save(new Item('o2modules', $this->registry, false));
                    }
                } else {
                    $this->registry = $this->fetchRegistry();
                    $cacheItemPool->save(new Item('o2modules', $this->registry, false));
                }
            } else {
                $this->registry = $this->fetchRegistry();
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::fetchRegistry
     *
     * Fetch modules registry.
     *
     * @return array
     */
    public function fetchRegistry()
    {
        $registry = [];
        $directory = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(PATH_APP),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $packagesIterator = new \RegexIterator($directory, '/^.+\.json/i', \RecursiveRegexIterator::GET_MATCH);

        foreach ($packagesIterator as $packageJsonFiles) {
            foreach ($packageJsonFiles as $packageJsonFile) {
                $packageJsonFile = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $packageJsonFile);
                $packageJsonFileInfo = pathinfo($packageJsonFile);

                if ($packageJsonFileInfo[ 'filename' ] === 'widget' or
                    $packageJsonFileInfo[ 'filename' ] === 'language' or
                    strpos($packageJsonFile, '.svn') !== false // subversion properties file conflict.
                ) {
                    continue;
                }

                if (is_cli()) {
                    output()->verbose(
                        (new Format())
                            ->setString(language()->getLine('CLI_REGISTRY_MODULE_VERB_FETCH_MANIFEST_START',
                                [str_replace(PATH_ROOT, '/', $packageJsonFile)]))
                            ->setNewLinesAfter(1)
                    );
                }

                $packageJsonMetadata = json_decode(file_get_contents($packageJsonFile), true);

                if (json_last_error() !== JSON_ERROR_NONE and is_cli()) {
                    output()->verbose(
                        (new Format())
                            ->setContextualClass(Format::DANGER)
                            ->setString(language()->getLine('CLI_REGISTRY_MODULE_VERB_FETCH_MANIFEST_FAILED'))
                            ->setIndent(2)
                            ->setNewLinesAfter(1)
                    );
                } elseif ( ! is_array($packageJsonMetadata)) {
                    continue;
                }

                if (strpos($packageJsonFile,
                        $modularType = ucfirst(plural($packageJsonFileInfo[ 'filename' ])) . DIRECTORY_SEPARATOR) === false) {
                    $modularType = ucfirst($packageJsonFileInfo[ 'filename' ]) . DIRECTORY_SEPARATOR;
                }

                $modularType = strtolower(rtrim($modularType, DIRECTORY_SEPARATOR));
                $this->addType($modularType);

                $moduleSegments = explode(
                    DIRECTORY_SEPARATOR,
                    trim(
                        str_replace(
                            [
                                PATH_FRAMEWORK,
                                PATH_PUBLIC,
                                PATH_RESOURCES,
                                PATH_APP,
                                $packageJsonFileInfo[ 'basename' ],
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

                $moduleParentSegments = [];

                foreach ([
                             'apps',
                             'modules',
                             'components',
                             'plugins',
                         ] as $moduleType
                ) {
                    if (false !== ($segmentKey = array_search($modularType, $moduleSegments))) {
                        $moduleParentSegments = array_slice($moduleSegments, 0, $segmentKey);

                        unset($moduleSegments[ $segmentKey ]);
                        break;
                    }
                }

                $moduleSegments = array_values($moduleSegments);

                $moduleNamespace = prepare_namespace(
                    str_replace(
                        PATH_ROOT,
                        '',
                        $packageJsonFileInfo[ 'dirname' ]
                    ),
                    false
                );

                if (isset($packageJsonMetadata[ 'namespace' ])) {
                    $moduleNamespace = $packageJsonMetadata[ 'namespace' ];
                    unset($packageJsonMetadata[ 'namespace' ]);
                }

                $registryKey = implode('/', $moduleSegments);

                if ($registryKey === '') {
                    if ($packageJsonFileInfo[ 'dirname' ] . DIRECTORY_SEPARATOR !== PATH_APP and $modularType === 'app') {
                        $registryKey = dash(snakecase(
                            pathinfo($packageJsonFileInfo[ 'dirname' ], PATHINFO_FILENAME)));
                    }
                }

                $registry[ $registryKey ] = (new DataStructures\Module(
                    $packageJsonFileInfo[ 'dirname' ]
                ))
                    ->setType($packageJsonFileInfo[ 'filename' ])
                    ->setNamespace($moduleNamespace)
                    ->setSegments($moduleSegments)
                    ->setParentSegments($moduleParentSegments)
                    ->setProperties($packageJsonMetadata);

                if (is_cli()) {
                    output()->verbose(
                        (new Format())
                            ->setContextualClass(Format::SUCCESS)
                            ->setString(language()->getLine('CLI_REGISTRY_MODULE_VERB_FETCH_MANIFEST_SUCCESS'))
                            ->setIndent(2)
                            ->setNewLinesAfter(1)
                    );
                }
            }
        }

        //ksort($registry);

        return $registry;
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::addType
     *
     * Add module type.
     *
     * @param string $type
     *
     * @return string
     */
    public function addType($type)
    {
        $pluralTypes = plural(strtolower($type));

        if ( ! in_array($pluralTypes, $this->types)) {
            array_push($this->types, $pluralTypes);
        }

        return $pluralTypes;
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::getRegistry
     *
     * Gets modules registries.
     *
     * @return array
     */
    public function getRegistry()
    {
        return $this->registry;
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::getTotalRegistry
     *
     * Gets total module registry.
     *
     * @return int
     */
    public function getTotalRegistry()
    {
        return count($this->registry);
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::updateRegistry
     *
     * Update module registry
     *
     * @return void
     * @throws \Exception
     */
    public function updateRegistry()
    {
        if (is_cli()) {
            output()->verbose(
                (new Format())
                    ->setContextualClass(Format::WARNING)
                    ->setString(language()->getLine('CLI_REGISTRY_MODULE_VERB_UPDATE_START'))
                    ->setNewLinesBefore(1)
                    ->setNewLinesAfter(2)
            );
        }

        $cacheItemPool = cache()->getObject('default');

        if (cache()->hasItemPool('registry')) {
            $cacheItemPool = cache()->getObject('registry');
        }

        if ($cacheItemPool instanceof CacheItemPoolInterface) {
            $this->registry = $this->fetchRegistry();
            $cacheItemPool->save(new Item('o2modules', $this->registry, false));
        }

        if (count($this->registry) and is_cli()) {
            output()->verbose(
                (new Format())
                    ->setContextualClass(Format::SUCCESS)
                    ->setString(language()->getLine('CLI_REGISTRY_MODULE_VERB_UPDATE_SUCCESS'))
                    ->setNewLinesBefore(1)
                    ->setNewLinesAfter(2)
            );
        } elseif (is_cli()) {
            output()->verbose(
                (new Format())
                    ->setContextualClass(Format::DANGER)
                    ->setString(language()->getLine('CLI_REGISTRY_MODULE_VERB_UPDATE_FAILED'))
                    ->setNewLinesBefore(1)
                    ->setNewLinesAfter(2)
            );
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::flushRegistry
     *
     * Flush modules registry.
     *
     * @return void
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function flushRegistry()
    {
        $cacheItemPool = cache()->getObject('default');

        if (cache()->hasItemPool('registry')) {
            $cacheItemPool = cache()->getObject('registry');
        }

        if ($cacheItemPool instanceof CacheItemPoolInterface) {
            $cacheItemPool->deleteItem('o2modules');
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::getApp
     *
     * @param string $segment
     *
     * @return bool|DataStructures\Module
     */
    public function getApp($segment)
    {
        $segment = dash($segment);

        if ($this->exists($segment)) {
            if ($this->registry[ $segment ] instanceof DataStructures\Module) {
                return $this->registry[ $segment ];
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::isExists
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
     * Modules::first
     *
     * @return bool|mixed
     */
    public function first()
    {
        if (isset($this->registry[ '' ])) {
            return $this->registry[ '' ];
        } elseif (reset($this->registry)->type === 'APP') {
            return reset($this->registry);
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::getModule
     *
     * @param string|array $segments
     *
     * @return bool|DataStructures\Module
     */
    public function getModule($segments)
    {
        $segments = (is_array($segments) ? implode('/', array_map('dash', $segments)) : $segments);

        if ($this->exists($segments)) {
            return $this->registry[ $segments ];
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
                $namespaces[ $key ] = new SplNamespaceInfo($module->getNamespace(), $module->getRealPath());
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
     * @param bool   $reverse
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
     * @param bool   $reverse
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

    // ------------------------------------------------------------------------

    /**
     * Modules::setActiveApp
     *
     * @param \O2System\Framework\Containers\Modules\DataStructures\Module $app
     *
     * @return static
     */
    public function setActiveApp(DataStructures\Module $app)
    {
        $this->activeApp = $app;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::setActiveApp
     *
     * @return \O2System\Framework\Containers\Modules\DataStructures\Module
     */
    public function getActiveApp()
    {
        return $this->activeApp;
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::setActiveModule
     *
     * @param \O2System\Framework\Containers\Modules\DataStructures\Module $module
     *
     * @return static
     */
    public function setActiveModule(DataStructures\Module $module)
    {
        $this->activeModule = $module;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::setActiveModule
     *
     * @return \O2System\Framework\Containers\Modules\DataStructures\Module
     */
    public function getActiveModule()
    {
        return $this->activeModule;
    }
}