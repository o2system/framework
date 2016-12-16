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

use O2System\Cache\Item;
use O2System\Framework\Registries;
use O2System\Psr\Cache\CacheItemPoolInterface;

class Language extends \O2System\Kernel\Services\Language
{
    private $registry = [ ];

    public function __construct ()
    {
        parent::__construct();

        $this->addFilePaths( [ PATH_FRAMEWORK, PATH_APP ] );
    }

    public function loadRegistry ()
    {
        $cacheHandler = cache()->get( 'default' );

        if ( cache()->has( 'registry' ) ) {
            $cacheHandler = cache()->get( 'registry' );
        }

        if ( $cacheHandler instanceof CacheItemPoolInterface ) {
            if ( $cacheHandler->hasItem( 'o2languages' ) ) {
                $this->registry = $cacheHandler->getItem( 'o2languages' )->get();
            } else {
                $this->registry = $this->fetchRegistry();
                $cacheHandler->save( new Item( 'o2languages', $this->registry, false ) );
            }
        } else {
            $this->registry = $this->fetchRegistry();
        }
    }

    public function fetchRegistry ()
    {
        $registry = [ ];
        $directory = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator( PATH_ROOT ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $packagesIterator = new \RegexIterator( $directory, '/^.+\.jsprop/i', \RecursiveRegexIterator::GET_MATCH );

        foreach ( $packagesIterator as $packageFilesProperties ) {
            foreach ( $packageFilesProperties as $packageFileProperties ) {
                $package = new Registries\Language( dirname( $packageFileProperties ) );

                if ( $package->isValid() ) {
                    $registry[ $package->getDirName() ] = $package;
                }
            }
        }

        ksort( $registry );

        return $registry;
    }

    public function isPackageExists ( $package )
    {
        return (bool) array_key_exists( $package, $this->packages );
    }

    public function getRegistry ()
    {
        return $this->registry;
    }

    public function countRegistry ()
    {
        return count( $this->registry );
    }

    public function updateRegistry ()
    {
        $cacheHandler = cache()->get( 'default' );

        if ( cache()->has( 'registry' ) ) {
            $cacheHandler = cache()->get( 'registry' );
        }

        if ( $cacheHandler instanceof CacheItemPoolInterface ) {
            $this->registry = $this->fetchRegistry();
            $cacheHandler->save( new Item( 'o2languages', $this->registry, false ) );
        }
    }

    public function flushRegistry ()
    {
        $cacheHandler = cache()->get( 'default' );

        if ( cache()->has( 'registry' ) ) {
            $cacheHandler = cache()->get( 'registry' );
        }

        if ( $cacheHandler instanceof CacheItemPoolInterface ) {
            $cacheHandler->deleteItem( 'o2languages' );
        }
    }
}