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
use O2System\Framework\Datastructures;
use O2System\Kernel\Cli\Writers\Format;
use O2System\Psr\Cache\CacheItemPoolInterface;

class Language extends \O2System\Kernel\Services\Language
{
    private $registry = [];

    public function __construct()
    {
        parent::__construct();

        $this->addFilePaths( [ PATH_FRAMEWORK, PATH_APP ] );
    }

    public function loadRegistry()
    {
        $cacheItemPool = cache()->getItemPool( 'default' );

        if ( cache()->hasItemPool( 'registry' ) ) {
            $cacheItemPool = cache()->getItemPool( 'registry' );
        }

        if ( $cacheItemPool instanceof CacheItemPoolInterface ) {
            if ( $cacheItemPool->hasItem( 'o2languages' ) ) {
                $this->registry = $cacheItemPool->getItem( 'o2languages' )->get();
            } else {
                $this->registry = $this->fetchRegistry();
                $cacheItemPool->save( new Item( 'o2languages', $this->registry, false ) );
            }
        } else {
            $this->registry = $this->fetchRegistry();
        }
    }

    public function fetchRegistry()
    {
        $registry = [];
        $directory = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator( PATH_ROOT ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $packagesIterator = new \RegexIterator( $directory, '/^.+\.jsprop/i', \RecursiveRegexIterator::GET_MATCH );

        foreach ( $packagesIterator as $packageFilesProperties ) {
            foreach ( $packageFilesProperties as $packageFileProperties ) {

                // filter fetch only language.jsprop filename
                if ( strpos( $packageFileProperties, 'language.jsprop' ) === false ) {
                    continue;
                }

                output()->verbose(
                    ( new Format() )
                        ->setString( language()->getLine( 'V_CLI_FETCH_LANGUAGE_MANIFEST',
                            [ str_replace( PATH_ROOT, '/', $packageFileProperties ) ] ) )
                        ->setNewLinesAfter( 1 )
                );

                $package = new Registries\Language( dirname( $packageFileProperties ) );

                if ( $package->isValid() ) {

                    output()->verbose(
                        ( new Format() )
                            ->setContextualClass( Format::SUCCESS )
                            ->setString( language()->getLine( 'V_CLI_FETCH_LANGUAGE_MANIFEST_SUCCESS' ) )
                            ->setIndent( 2 )
                            ->setNewLinesAfter( 1 )
                    );

                    $registry[ $package->getDirName() ] = $package;
                } else {
                    output()->verbose(
                        ( new Format() )
                            ->setContextualClass( Format::DANGER )
                            ->setString( language()->getLine( 'V_CLI_FETCH_LANGUAGE_MANIFEST_FAILED' ) )
                            ->setIndent( 2 )
                            ->setNewLinesAfter( 1 )
                    );
                }
            }
        }

        ksort( $registry );

        return $registry;
    }

    public function isPackageExists( $package )
    {
        return (bool)array_key_exists( $package, $this->packages );
    }

    public function getRegistry()
    {
        return $this->registry;
    }

    public function countRegistry()
    {
        return count( $this->registry );
    }

    public function updateRegistry()
    {
        output()->verbose(
            ( new Format() )
                ->setContextualClass( Format::WARNING )
                ->setString( language()->getLine( 'V_CLI_START_UPDATE_LANGUAGE_REGISTRY' ) )
                ->setNewLinesBefore( 1 )
                ->setNewLinesAfter( 2 )
        );

        $cacheItemPool = cache()->getObject( 'default' );

        if ( cache()->exists( 'registry' ) ) {
            $cacheItemPool = cache()->getObject( 'registry' );
        }

        if ( $cacheItemPool instanceof CacheItemPoolInterface ) {
            $this->registry = $this->fetchRegistry();
            $cacheItemPool->save( new Item( 'o2languages', $this->registry, false ) );
        }

        if ( count( $this->registry ) ) {
            output()->verbose(
                ( new Format() )
                    ->setContextualClass( Format::SUCCESS )
                    ->setString( language()->getLine( 'V_CLI_SUCCESS_UPDATE_LANGUAGE_REGISTRY' ) )
                    ->setNewLinesBefore( 1 )
                    ->setNewLinesAfter( 2 )
            );
        } else {
            output()->verbose(
                ( new Format() )
                    ->setContextualClass( Format::DANGER )
                    ->setString( language()->getLine( 'V_CLI_FAILED_UPDATE_LANGUAGE_REGISTRY' ) )
                    ->setNewLinesBefore( 1 )
                    ->setNewLinesAfter( 2 )
            );
        }
    }

    public function flushRegistry()
    {
        $cacheItemPool = cache()->getItemPool( 'default' );

        if ( cache()->exists( 'registry' ) ) {
            $cacheItemPool = cache()->getItemPool( 'registry' );
        }

        if ( $cacheItemPool instanceof CacheItemPoolInterface ) {
            $cacheItemPool->deleteItem( 'o2languages' );
        }
    }
}