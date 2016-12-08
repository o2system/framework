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

namespace O2System\Framework\Http\Router\Registries;


use O2System\Spl\Datastructures\SplArrayObject;
use O2System\Spl\Info\SplFileInfo;

class Page extends SplFileInfo
{
    /**
     * Page Variables
     *
     * @var SplArrayObject
     */
    private $vars = [ ];

    /**
     * Page Settings
     *
     * @var SplArrayObject
     */
    private $settings;

    public function __construct ( $filename )
    {
        parent::__construct( $filename );

        if ( file_exists(
            $propsFilePath = $this->getPath() . DIRECTORY_SEPARATOR . str_replace(
                    '.phtml',
                    '.jspage',
                    strtolower( $this->getBasename() )
                )
        ) ) {
            $props = file_get_contents( $propsFilePath );
            $props = json_decode( $props, true );

            if ( isset( $props[ 'vars' ] ) ) {
                $this->vars = $props[ 'vars' ];
            }

            if ( isset( $props[ 'settings' ] ) ) {
                $this->settings = new SplArrayObject( $props[ 'settings' ] );
            }
        }
    }

    public function getVars ()
    {
        return $this->vars;
    }

    public function getSettings ()
    {
        if ( $this->settings instanceof SplArrayObject ) {
            return $this->settings;
        }

        return false;
    }
}