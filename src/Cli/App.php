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

namespace O2System\Framework\Cli;

// ------------------------------------------------------------------------

use O2System\Kernel\Cli\Writers\Formatter;
use O2System\Kernel\Cli\Writers\Table;

/**
 * Class App
 *
 * Command line interface (cli) application commands container class.
 *
 * @package O2System\Kernel\Cli
 */
class App
{
    /**
     * App::$logo
     *
     * Command line interface (cli) application logo.
     *
     * @var string
     */
    protected $logo;

    /**
     * App::$name
     *
     * Command line interface (cli) application name.
     *
     * @var string
     */
    protected $name;

    /**
     * App::$version
     *
     * Command line interface (cli) application version.
     *
     * @var string
     */
    protected $version;

    /**
     * App::$description
     *
     * Command line interface (cli) application description.
     *
     * @var string
     */
    protected $description;

    /**
     * App::$commands
     *
     * Array of application commands line interface.
     *
     * @var array
     */
    protected $commands = [];

    // ------------------------------------------------------------------------

    /**
     * App::__construct
     *
     * Command line interface (cli) application constructor.
     */
    public function __construct ()
    {
        language()->loadFile( 'cli' );
    }

    // ------------------------------------------------------------------------

    /**
     * App::hasCommand
     *
     * Check whether the application has a command that you're looking for.
     *
     * @param string $command Command array offset key.
     *
     * @return bool
     */
    public function hasCommand ( $command )
    {
        return (bool) array_key_exists( $command, $this->commands );
    }

    // ------------------------------------------------------------------------

    /**
     * App::registerCommand
     *
     * @param Command $command
     */
    public function addCommand ( Command $command )
    {
        $this->commands[ $command->getCaller() ] = $command;
    }

    public function setAppLogo ( $logo )
    {
        $this->logo = $logo;

        return $this;
    }

    // ------------------------------------------------------------------------

    public function setAppName ( $name )
    {
        $this->name = trim( $name );

        return $this;
    }

    // ------------------------------------------------------------------------

    public function setAppVersion ( $version )
    {
        $this->version = trim( $version );

        return $this;
    }

    // ------------------------------------------------------------------------

    public function setPoolsDescription ( $description )
    {
        $this->description = trim( $description );

        return $this;
    }

    // ------------------------------------------------------------------------

    public function loadCommands ( $namespace, $commandsPath )
    {
        if ( is_dir( $commandsPath ) ) {
            $namespace = $namespace . rtrim( '\\', $namespace ) . '\\';
            $commandsPath = rtrim( $commandsPath, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;

            foreach ( glob( $commandsPath . '*.php' ) as $filePath ) {
                if ( is_file( $filePath ) ) {
                    $commandClassName = $namespace . pathinfo( $filePath, PATHINFO_FILENAME );
                    $this->addCommand( new $commandClassName );
                }
            }
        }
    }

    // ------------------------------------------------------------------------

    public function run ()
    {
        $argv = input()->argv();

        if ( count( $argv ) == 0 ) {
            // Show Logo
            if ( ! empty( $this->logo ) ) {
                output()->writeln( PHP_EOL . $this->logo );
            }

            // Show Name & Version Line
            output()->writeln( PHP_EOL . $this->name . ' v' . $this->version . PHP_EOL );

            $this->optionHelp();
        } else {
            $command = input()->getCommand();

            if ( $this->hasCommand( $command ) ) {
                if ( input()->getOptions( 'version' ) or input()->getOptions( 'v' ) ) {
                    $this->commands[ $command ]->optionVersion();
                    exit( EXIT_SUCCESS );
                } elseif ( input()->getOptions( 'help' ) or input()->getOptions( 'h' ) ) {
                    $this->commands[ $command ]->optionHelp();
                    exit( EXIT_SUCCESS );
                }
                call_user_func_array( [ &$this->commands[ $command ], 'callOptions' ], [ input()->getOptions() ] );
            } elseif ( $command === '--help' or $command === '-h' ) {
                output()->writeln( PHP_EOL );
                $this->optionHelp();
                exit( EXIT_SUCCESS );
            } elseif ( $command === '--version' or $command === '-v' ) {
                $this->optionVersion();
                exit( EXIT_SUCCESS );
            } elseif ( $command === '--serve' or $command === '-sv' ) {
                $this->optionServe();
                exit( EXIT_SUCCESS );
            }
        }
    }

    // ------------------------------------------------------------------------

    final public function optionVersion ()
    {
        if ( property_exists( $this, 'version' ) ) {
            if ( ! empty( $this->version ) ) {
                // Show Name & Version Line
                output()->writeln( PHP_EOL . ' v' . $this->version );
            }
        }
    }

    // ------------------------------------------------------------------------

    final public function optionHelp ()
    {
        $formatter = new Formatter();

        // Show Usage
        output()->writeln( language()->getLine( 'CLI_USAGE' ) . ':' );
        output()->writeln(
            $formatter
                ->setIndent( 1 )
                ->format( 'command --option argument' . PHP_EOL )
        );

        // Show Options
        output()->writeln( language()->getLine( 'CLI_OPTIONS' ) . ':' );

        $table = new Table();
        $table->isShowBorder = false;

        $table
            ->addRow()
            ->addColumn( '--version' )
            ->addColumn( '-v' )
            ->addColumn( 'display version' )
            ->addRow()
            ->addColumn( '--help' )
            ->addColumn( '-h' )
            ->addColumn( 'display help' )
            ->addRow()
            ->addColumn( '--verbose' )
            ->addColumn( '-vv' )
            ->addColumn( 'display with verbose' )
            ->addColumn( '--serve' )
            ->addColumn( '-sv' )
            ->addColumn( 'php web-server launcher' );

        output()->writeln(
            $formatter
                ->setIndent( 1 )
                ->format( $table->render() . PHP_EOL )
        );

        // Show Commands
        output()->writeln( language()->getLine( 'CLI_COMMANDS' ) . ':' );

        $table = new Table();
        $table->isShowBorder = false;

        foreach ( $this->commands as $command ) {
            $table
                ->addRow()
                ->addColumn( $command->getCaller() )
                ->addColumn( $command->getDescription() );
        }

        output()->writeln(
            $formatter
                ->setIndent( 1 )
                ->format( $table->render() . PHP_EOL )
        );
    }

    // ------------------------------------------------------------------------

    public function optionServe ()
    {
        /*
         * Collect any user-supplied options and apply them
         */
        $options['host'] = input()->getOptions( 'host' );
        $options['port'] = input()->getOptions( 'port' );

        $host = empty( $options['host'] )
            ? 'localhost'
            : $options['host'];

        $port = empty( $options['port'] )
            ? 8080
            : $options['port'];

        output()->writeln( "O2System Framework development server started on http://{$host}:{$port}" );

        $_SERVER['DOCUMENT_ROOT'] = PATH_PUBLIC;
        output()->writeln( "Document Root on " . $_SERVER['DOCUMENT_ROOT'] );
        output()->writeln( "Press Control-C to stop." );

        /*
         * Call PHP's built-in webserver, making sure to set our
         * base path to the public folder, and to use the rewrite file
         * to ensure our environment is set and it simulates basic mod_rewrite.
         */
        passthru( 'php -S ' . $host . ':' . $port . ' -t ' . str_replace( '\\', '/', DIR_PUBLIC ) . '/' );
    }
}