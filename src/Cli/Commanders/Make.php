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

namespace O2System\Framework\Cli\Commanders;

// ------------------------------------------------------------------------

use O2System\Framework\Cli\Commander;

/**
 * Class Make
 *
 * @package O2System\Framework\Cli\Commanders
 */
class Make extends Commander
{
    /**
     * Make::$commandVersion
     *
     * Command version.
     *
     * @var string
     */
    protected $commandVersion = '1.0.0';

    /**
     * Make::$commandDescription
     *
     * Command description.
     *
     * @var string
     */
    protected $commandDescription = 'DESC_CLI_MAKE';

    /**
     * Make::$commandOptions
     *
     * Command options.
     *
     * @var array
     */
    protected $commandOptions = [
        'name'      => [
            'description' => 'Set a filename',
            'required'    => true,
        ],
        'path'      => [
            'description' => 'Set a custom path file',
            'required'    => false,
        ],
        'filename'  => [
            'description' => 'Set a filename',
            'required'    => true,
        ],
        'namespace' => [
            'description' => 'Set a custom namespace',
            'shortcut'    => 'ns',
            'required'    => false,
        ],
    ];

    /**
     * Make::$path
     *
     * Make path.
     *
     * @var string
     */
    protected $optionPath;

    /**
     * Make::$filename
     *
     * Make filename.
     *
     * @var string
     */
    protected $optionFilename;

    public function optionPath( $path )
    {
        $path = str_replace( [ '\\', '/' ], DIRECTORY_SEPARATOR, $path );
        $path = PATH_ROOT . str_replace( PATH_ROOT, '', $path );

        if ( pathinfo( $path, PATHINFO_EXTENSION ) ) {
            $this->optionFilename( pathinfo( $path, PATHINFO_FILENAME ) );
            $path = dirname( $path );
        }

        $this->optionPath = rtrim( $path, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
    }

    public function optionFilename( $name )
    {
        $name = str_replace( '.php', '', $name );
        $this->optionFilename = prepare_filename( $name ) . '.php';

        $this->optionPath = empty( $this->optionPath ) ? modules()->current()->getRealPath() : $this->optionPath;
    }

    public function optionName( $name )
    {
        $this->optionFilename( $name );
    }

    public function optionNamespace( $namespace )
    {
        $this->namespace = $namespace;
    }
}