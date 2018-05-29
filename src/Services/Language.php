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

namespace O2System\Framework\Services;

// ------------------------------------------------------------------------

use O2System\Cache\Item;
use O2System\Framework\Datastructures;
use O2System\Kernel\Cli\Writers\Format;
use O2System\Psr\Cache\CacheItemPoolInterface;

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
     */
    public function loadRegistry()
    {
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
        $directory = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(PATH_ROOT),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $packagesIterator = new \RegexIterator($directory, '/^.+\.jsprop/i', \RecursiveRegexIterator::GET_MATCH);

        foreach ($packagesIterator as $packageFilesProperties) {
            foreach ($packageFilesProperties as $packageFileProperties) {

                // filter fetch only language.jsprop filename
                if (strpos($packageFileProperties, 'language.jsprop') === false) {
                    continue;
                }

                if (is_cli()) {
                    output()->verbose(
                        (new Format())
                            ->setString(language()->getLine('CLI_REGISTRY_LANGUAGE_VERB_FETCH_MANIFEST_START',
                                [str_replace(PATH_ROOT, '/', $packageFileProperties)]))
                            ->setNewLinesAfter(1)
                    );
                }

                $package = new Datastructures\Language(dirname($packageFileProperties));

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

        ksort($registry);

        return $registry;
    }

    // ------------------------------------------------------------------------

    /**
     * Language::isPackageExists
     *
     * @param $package
     *
     * @return bool
     */
    public function packageExists($package)
    {
        return isset($this->registry[ $package ]);
    }

    // ------------------------------------------------------------------------

    /**
     * Language::getRegistry
     *
     * Gets language registries.
     *
     * @return array
     */
    public function getRegistry()
    {
        return $this->registry;
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