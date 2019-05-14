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

namespace O2System\Framework\Services;

// ------------------------------------------------------------------------

use O2System\Cache\Item;
use O2System\Framework\DataStructures;
use O2System\Kernel\Cli\Writers\Format;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class Language
 *
 * @package O2System\Framework\Services
 */
class Language extends \O2System\Kernel\Services\Language
{
    /**
     * Language::$registry
     *
     * Language registries.
     *
     * @var array
     */
    private $registry = [];

    // ------------------------------------------------------------------------

    /**
     * Language::__construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->addFilePaths([PATH_FRAMEWORK, PATH_APP]);
    }

    // ------------------------------------------------------------------------

    /**
     * Language::loadRegistry
     *
     * Load language registry.
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
                if ($cacheItemPool->hasItem('o2languages')) {
                    $this->registry = $cacheItemPool->getItem('o2languages')->get();
                } else {
                    $this->registry = $this->fetchRegistry();
                    $cacheItemPool->save(new Item('o2languages', $this->registry, false));
                }
            } else {
                $this->registry = $this->fetchRegistry();
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Language::fetchRegistry
     *
     * Fetch language registry.
     *
     * @return array
     */
    public function fetchRegistry()
    {
        $registry = [];

        $directory = new \RecursiveIteratorIterator(new \RecursiveCallbackFilterIterator(
            new \RecursiveDirectoryIterator(PATH_ROOT,
                \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS),
            function ($current, $key, $iterator) {
                if ($current->isDir()) {
                    // exclude build directory
                    if (in_array($current->getFilename(), [
                        'node_modules'
                    ])) {
                        return false;
                    }
                }

                return true;
            }));

        $packagesIterator = new \RegexIterator($directory, '/^.+\.json$/i', \RecursiveRegexIterator::GET_MATCH);

        foreach ($packagesIterator as $packageJsonFiles) {
            foreach ($packageJsonFiles as $packageJsonFile) {
                $packageJsonFile = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $packageJsonFile);
                $packageJsonFileInfo = pathinfo($packageJsonFile);

                $files[] = $packageJsonFileInfo;

                if ($packageJsonFileInfo[ 'filename' ] === 'language') {
                    if (is_cli()) {
                        output()->verbose(
                            (new Format())
                                ->setString(language()->getLine('CLI_REGISTRY_LANGUAGE_VERB_FETCH_MANIFEST_START',
                                    [str_replace(PATH_ROOT, '/', $packageJsonFile)]))
                                ->setNewLinesAfter(1)
                        );
                    }

                    $package = new DataStructures\Language(dirname($packageJsonFile));

                    if ($package->isValid()) {
                        if (is_cli()) {
                            output()->verbose(
                                (new Format())
                                    ->setContextualClass(Format::SUCCESS)
                                    ->setString(language()->getLine('CLI_REGISTRY_LANGUAGE_VERB_FETCH_MANIFEST_SUCCESS'))
                                    ->setIndent(2)
                                    ->setNewLinesAfter(1)
                            );
                        }

                        $registry[ $package->getDirName() ] = $package;
                    } elseif (is_cli()) {
                        output()->verbose(
                            (new Format())
                                ->setContextualClass(Format::DANGER)
                                ->setString(language()->getLine('CLI_REGISTRY_LANGUAGE_VERB_FETCH_MANIFEST_FAILED'))
                                ->setIndent(2)
                                ->setNewLinesAfter(1)
                        );
                    }
                }
            }
        }

        ksort($registry);

        return $registry;
    }

    // ------------------------------------------------------------------------

    /**
     * Language::getDefaultMetadata
     *
     * @return array
     */
    public function getDefaultMetadata()
    {
        return $this->getRegistry($this->getDefault());
    }

    // ------------------------------------------------------------------------

    /**
     * Language::getRegistry
     *
     * Gets language registries.
     *
     * @return array
     */
    public function getRegistry($package = null)
    {
        if (isset($package)) {
            if ($this->registered($package)) {
                return $this->registry[ $package ];
            }

            return false;
        }

        return $this->registry;
    }

    // ------------------------------------------------------------------------

    /**
     * Language::registered
     *
     * @param $package
     *
     * @return bool
     */
    public function registered($package)
    {
        return isset($this->registry[ $package ]);
    }

    // ------------------------------------------------------------------------

    /**
     * Language::getTotalRegistry
     *
     * Gets num of registries.
     *
     * @return int
     */
    public function getTotalRegistry()
    {
        return count($this->registry);
    }

    // ------------------------------------------------------------------------

    /**
     * Language::updateRegistry
     *
     * Update language registry.
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
                    ->setString(language()->getLine('CLI_REGISTRY_LANGUAGE_VERB_UPDATE_START'))
                    ->setNewLinesBefore(1)
                    ->setNewLinesAfter(2)
            );
        }

        $cacheItemPool = cache()->getObject('default');

        if (cache()->exists('registry')) {
            $cacheItemPool = cache()->getObject('registry');
        }

        if ($cacheItemPool instanceof CacheItemPoolInterface) {
            $this->registry = $this->fetchRegistry();
            $cacheItemPool->save(new Item('o2languages', $this->registry, false));
        }

        if (count($this->registry) and is_cli()) {
            output()->verbose(
                (new Format())
                    ->setContextualClass(Format::SUCCESS)
                    ->setString(language()->getLine('CLI_REGISTRY_LANGUAGE_VERB_UPDATE_SUCCESS'))
                    ->setNewLinesBefore(1)
                    ->setNewLinesAfter(2)
            );
        } elseif (is_cli()) {
            output()->verbose(
                (new Format())
                    ->setContextualClass(Format::DANGER)
                    ->setString(language()->getLine('CLI_REGISTRY_LANGUAGE_VERB_UPDATE_FAILED'))
                    ->setNewLinesBefore(1)
                    ->setNewLinesAfter(2)
            );
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Language::flushRegistry
     *
     * Flush language registry.
     *
     * @return void
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function flushRegistry()
    {
        $cacheItemPool = cache()->getItemPool('default');

        if (cache()->exists('registry')) {
            $cacheItemPool = cache()->getItemPool('registry');
        }

        if ($cacheItemPool instanceof CacheItemPoolInterface) {
            $cacheItemPool->deleteItem('o2languages');
        }
    }
}