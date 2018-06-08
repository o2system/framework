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

namespace O2System\Framework\Containers;

// ------------------------------------------------------------------------

use O2System\Cache\Item;
use O2System\Framework\Datastructures;
use O2System\Framework\Services\Hooks;
use O2System\Kernel\Cli\Writers\Format;
use O2System\Kernel\Http\Router\Addresses;
use O2System\Psr\Cache\CacheItemPoolInterface;
use O2System\Spl\Containers\Datastructures\SplServiceRegistry;
use O2System\Spl\Datastructures\SplArrayStack;
use O2System\Spl\Info\SplNamespaceInfo;

/**
 * Class Modules
 *
 * @package O2System\Kernel
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

    // ------------------------------------------------------------------------

    /**
     * Modules::__construct
     */
    public function __construct()
    {
        parent::__construct(
            [
                (new Datastructures\Module(PATH_KERNEL))
                    ->setType('KERNEL')
                    ->setNamespace('O2System\Kernel\\'),
                (new Datastructures\Module(PATH_FRAMEWORK))
                    ->setType('FRAMEWORK')
                    ->setNamespace('O2System\Framework\\'),
                (new Datastructures\Module(PATH_APP))
                    ->setType('APP')
                    ->setNamespace('App'),
            ]
        );
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
        // Register Framework\Services\Loader Namespace
        loader()->addNamespace($module->getNamespace(), $module->getRealPath());

        // Autoload Module Helpers
        $this->autoloadHelpers($module);

        if ( ! in_array($module->getType(), ['KERNEL', 'FRAMEWORK'])) {

            // Add Public Dir
            loader()->addPublicDir($module->getPublicDir());

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

        parent::push($module);
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::autoloadHelpers
     *
     * Autoload modules helpers.
     *
     * @param \O2System\Framework\Datastructures\Module $module
     */
    private function autoloadHelpers(Datastructures\Module $module)
    {
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
     * @param \O2System\Framework\Datastructures\Module $module
     */
    private function autoloadConfig(Datastructures\Module $module)
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
     * @param \O2System\Framework\Datastructures\Module $module
     */
    private function autoloadAddresses(Datastructures\Module $module)
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
     * @param \O2System\Framework\Datastructures\Module $module
     */
    private function autoloadHooks(Datastructures\Module $module)
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
     * @param \O2System\Framework\Datastructures\Module $module
     */
    private function autoloadModels(Datastructures\Module $module)
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
                if (class_exists($model)) {
                    $service = new SplServiceRegistry($model);

                    if ($service->isSubclassOf('O2System\Framework\Models\Sql\Model') ||
                        $service->isSubclassOf('O2System\Framework\Models\NoSql\Model') ||
                        $service->isSubclassOf('O2System\Framework\Models\Files\Model')
                    ) {
                        models()->attach($offset, $service);
                    }
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
     * @param \O2System\Framework\Datastructures\Module $module
     */
    private function autoloadServices(Datastructures\Module $module)
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
                    if ( ! class_exists($service)) {
                        continue;
                    }
                }

                o2system()->addService($service, $offset);
            }

            unset($services);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::loadRegistry
     *
     * Load modules registry
     *
     * @return void
     * @throws \O2System\Psr\Cache\InvalidArgumentException
     */
    public function loadRegistry()
    {
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
        $datastructures = [];
        $directory = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(PATH_APP),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $propertiesIterator = new \RegexIterator($directory, '/^.+\.jsprop/i', \RecursiveRegexIterator::GET_MATCH);

        foreach ($propertiesIterator as $propertiesFiles) {
            foreach ($propertiesFiles as $propertiesFile) {

                $propertiesFile = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $propertiesFile);
                $propertiesFileInfo = pathinfo($propertiesFile);

                if ($propertiesFileInfo[ 'filename' ] === 'widget' or
                    $propertiesFileInfo[ 'filename' ] === 'language' or
                    strpos($propertiesFile, '.svn') !== false // subversion properties file conflict.
                ) {
                    continue;
                }

                if (is_cli()) {
                    output()->verbose(
                        (new Format())
                            ->setString(language()->getLine('CLI_REGISTRY_MODULE_VERB_FETCH_MANIFEST_START',
                                [str_replace(PATH_ROOT, '/', $propertiesFile)]))
                            ->setNewLinesAfter(1)
                    );
                }

                $propertiesMetadata = json_decode(file_get_contents($propertiesFile), true);

                if (json_last_error() !== JSON_ERROR_NONE and is_cli()) {
                    output()->verbose(
                        (new Format())
                            ->setContextualClass(Format::DANGER)
                            ->setString(language()->getLine('CLI_REGISTRY_MODULE_VERB_FETCH_MANIFEST_FAILED'))
                            ->setIndent(2)
                            ->setNewLinesAfter(1)
                    );
                }

                $moduleSegments = explode(
                    DIRECTORY_SEPARATOR,
                    trim(
                        str_replace(
                            [
                                PATH_FRAMEWORK,
                                PATH_PUBLIC,
                                PATH_APP,
                                $propertiesFileInfo[ 'basename' ],
                            ],
                            '',
                            $propertiesFile
                        ),
                        DIRECTORY_SEPARATOR
                    )
                );

                array_shift($moduleSegments);

                $moduleSegments = array_map(function ($string) {
                    return dash(snakecase($string));
                }, $moduleSegments);

                $moduleNamespace = prepare_namespace(
                    str_replace(
                        PATH_ROOT,
                        '',
                        $propertiesFileInfo[ 'dirname' ]
                    ),
                    false
                );

                if (isset($propertiesMetadata[ 'namespace' ])) {
                    $moduleNamespace = $propertiesMetadata[ 'namespace' ];
                    unset($propertiesMetadata[ 'namespace' ]);
                }

                $modulePluralTypes = $this->addType($propertiesFileInfo[ 'filename' ]);

                $moduleParentSegments = [];
                if (false !== ($moduleTypeSegmentKey = array_search($modulePluralTypes, $moduleSegments))) {
                    $moduleParentSegments = array_slice($moduleSegments, 0, $moduleTypeSegmentKey);

                    $moduleParentSegments = array_diff($moduleParentSegments, $this->types);
                    $moduleSegments = array_diff($moduleSegments, $this->types);
                }

                $registryKey = implode('/', $moduleSegments);

                if ($registryKey === '') {
                    if ($propertiesFileInfo[ 'dirname' ] . DIRECTORY_SEPARATOR !== PATH_APP and $modulePluralTypes === 'apps') {
                        $registryKey = 'apps/' . dash(snakecase(
                                pathinfo($propertiesFileInfo[ 'dirname' ], PATHINFO_FILENAME)));
                    }
                } else {
                    $registryKey = 'modules/' . $registryKey;
                }

                $datastructures[ $registryKey ] = (new Datastructures\Module(
                    $propertiesFileInfo[ 'dirname' ]
                ))
                    ->setType($propertiesFileInfo[ 'filename' ])
                    ->setNamespace($moduleNamespace)
                    ->setSegments($moduleSegments)
                    ->setParentSegments($moduleParentSegments)
                    ->setProperties($propertiesMetadata);

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

        ksort($datastructures);

        return $datastructures;
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
     * @throws \O2System\Psr\Cache\InvalidArgumentException
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
     * @return bool|Datastructures\Module
     */
    public function getApp($segment)
    {
        $segment = 'apps/' . dash($segment);

        if ($this->exists($segment)) {
            if ($this->registry[ $segment ] instanceof Datastructures\Module) {
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

        return (bool)array_key_exists($segments, $this->registry);
    }

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
     * @return bool|Datastructures\Module
     */
    public function getModule($segments)
    {
        $segments = 'modules/' . (is_array($segments) ? implode('/', array_map('dash', $segments)) : $segments);

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
            if ($module instanceof Datastructures\Module) {
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
     * @param string $dirName
     * @param bool   $reverse
     *
     * @return array
     */
    public function getDirs($dirName, $reverse = false)
    {
        $dirs = [];
        $dirName = prepare_class_name($dirName);
        $dirName = str_replace(
            ['\\', '/'],
            DIRECTORY_SEPARATOR,
            $dirName
        );

        foreach ($this as $module) {
            if ($module instanceof Datastructures\Module) {
                if (is_dir($dirPath = $module->getRealPath() . $dirName)) {
                    $dirs[] = $dirPath . DIRECTORY_SEPARATOR;
                }

                if (is_dir($dirPath = $module->getRealPath() . (is_cli() ? 'Cli' : 'Http') . DIRECTORY_SEPARATOR . $dirName)) {
                    $dirs[] = $dirPath . DIRECTORY_SEPARATOR;
                }
            }
        }

        return $reverse === true ? array_reverse($dirs) : $dirs;
    }
}