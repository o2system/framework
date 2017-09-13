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

use O2System\Kernel\Cli\Abstracts\AbstractCommander;
use O2System\Kernel\Cli\Abstracts\AbstractCommandersPool;
use O2System\Kernel\Cli\Writers\Form;
use O2System\Kernel\Cli\Writers\Format;
use O2System\Kernel\Cli\Writers\ProgressBar;
use O2System\Kernel\Cli\Writers\Table;
use O2System\Kernel\Cli\Writers\Text;

/**
 * Class App
 *
 * Command line interface (cli) application commands container class.
 *
 * @package O2System\Kernel\Cli
 */
class App extends AbstractCommandersPool
{
    /**
     * App::$logo
     *
     * Cli-App welcome note.
     *
     * @var string
     */
    protected $welcomeNote;

    /**
     * App::$name
     *
     * Cli-App name.
     *
     * @var string
     */
    protected $name;

    /**
     * App::$version
     *
     * Cli-App version.
     *
     * @var string
     */
    protected $version;

    /**
     * App::$description
     *
     * Cli-App description.
     *
     * @var string
     */
    protected $description;

    // ------------------------------------------------------------------------

    /**
     * App::__construct
     *
     * Cli-App constructor.
     */
    public function __construct()
    {
        language()->loadFile( 'cli' );
    }

    // ------------------------------------------------------------------------

    /**
     * App::addCommander
     *
     * Add new commander to the pool.
     *
     * @param AbstractCommander $commander
     */
    public function addCommander( AbstractCommander $commander )
    {
        if ( method_exists( $commander, 'setApp' ) ) {
            $commander->setApp( $this );
        }

        $this->commandersPool[ $commander->getCommandName() ] = $commander;
    }

    // ------------------------------------------------------------------------

    public function getWelcomeNote()
    {
        return $this->welcomeNote;
    }

    // ------------------------------------------------------------------------

    /**
     * App::setWelcomeNote
     *
     * Sets cli-app welcome note.
     *
     * @param string $welcomeNote
     *
     * @return static
     */
    public function setWelcomeNote( $welcomeNote )
    {
        $this->welcomeNote = $welcomeNote;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    // ------------------------------------------------------------------------

    /**
     * App::setName
     *
     * Sets cli-app name.
     *
     * @param string $name
     *
     * @return static
     */
    public function setName( $name )
    {
        $this->name = trim( $name );

        return $this;
    }

    /**
     * App::getVersion
     *
     * Gets cli-app version.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    // ------------------------------------------------------------------------

    /**
     * App::setVersion
     *
     * Sets cli-app version.
     *
     * @param string $version
     *
     * @return static
     */
    public function setVersion( $version )
    {
        $this->version = trim( $version );

        return $this;
    }

    /**
     * App::setDescription
     *
     * Sets cli-app description.
     *
     * @param string $description
     *
     * @return static
     */
    public function setDescription( $description )
    {
        $this->description = trim( $description );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * App::run
     *
     * Run cli-app
     *
     * @return static
     */
    public function run()
    {
        $command = new \ReflectionClass( $this );
        $options = input()->get();

        if ( empty( $options ) ) {
            if ( $this->welcomeNote instanceof Format or $this->welcomeNote instanceof Text ) {
                output()->write( $this->welcomeNote );
            } elseif ( is_string( $this->welcomeNote ) and $this->welcomeNote !== '' ) {
                output()->write(
                    ( new Format() )
                        ->setString( language()->getLine( $this->welcomeNote ) )
                        ->setNewLinesAfter( 1 )
                );
            }

            output()->write(
                ( new Format() )
                    ->setString( $this->name . ' v' . $this->version )
                    ->setNewLinesBefore( 2 )
                    ->setNewLinesAfter( 1 )
            );

            // Run help option
            $this->optionHelp();
        } else {

            foreach ( $options as $method => $arguments ) {

                if ( $method === 'h' ) {
                    $method = 'help';
                } elseif ( $method === 'v' ) {
                    $method = 'version';
                } elseif ( $method === 'vv' ) {
                    $method = 'verbose';
                }

                $optionMethod = camelcase( 'option-' . $method );

                if ( $command->hasMethod( $optionMethod ) ) {

                    $commandMethod = $command->getMethod( $optionMethod );

                    if ( $commandMethod->getNumberOfRequiredParameters() == 0 ) {
                        call_user_func( [ &$this, $optionMethod ] );
                    } else {
                        $optionArguments = is_array( $arguments )
                            ? $arguments
                            : [ $arguments ];

                        call_user_func_array( [ &$this, $optionMethod ], $optionArguments );
                    }
                }
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * App::optionHelp
     *
     * @return void
     */
    final public function optionHelp()
    {
        // Show Usage
        output()->write(
            ( new Format() )
                ->setContextualClass( Format::INFO )
                ->setString( language()->getLine( 'CLI_USAGE' ) . ':' )
                ->setNewLinesBefore( 1 )
                ->setNewLinesAfter( 1 )
        );

        output()->write(
            ( new Format() )
                ->setContextualClass( Format::INFO )
                ->setString( 'command:action --option=value' )
                ->setNewLinesAfter( 2 )
        );

        // Show Commanders
        $this->loadCommanders();

        if ( count( $this->commandersPool ) ) {
            output()->write(
                ( new Format() )
                    ->setString( language()->getLine( 'CLI_COMMANDS' ) . ':' )
                    ->setNewLinesAfter( 1 )
            );

            $table = new Table();
            $table->isShowBorder = false;

            foreach ( $this->commandersPool as $commander ) {

                if ( $commander instanceof AbstractCommander ) {
                    $table
                        ->addRow()
                        ->addColumn( $commander->getCommandName() )
                        ->addColumn( language()->getLine( $commander->getCommandDescription() ) );
                }
            }

            output()->write(
                ( new Format() )
                    ->setString( $table->render() )
                    ->setNewLinesAfter( 2 )
            );
        }

        // Show Options
        output()->write(
            ( new Format() )
                ->setString( language()->getLine( 'CLI_OPTIONS' ) . ':' )
                ->setNewLinesAfter( 1 )
        );

        $table = new Table();
        $table->isShowBorder = false;

        $table
            ->addRow()
            ->addColumn( '--version' )
            ->addColumn( '-v' )
            ->addColumn( language()->getLine( 'H_CLI_OPTION_VERSION' ) )
            ->addRow()
            ->addColumn( '--help' )
            ->addColumn( '-h' )
            ->addColumn( language()->getLine( 'H_CLI_OPTION_HELP' ) )
            ->addRow()
            ->addColumn( '--verbose' )
            ->addColumn( '-vv' )
            ->addColumn( language()->getLine( 'H_CLI_OPTION_VERBOSE' ) );

        output()->write(
            ( new Format() )
                ->setString( $table->render() )
                ->setNewLinesAfter( 2 )
        );

        exit( EXIT_SUCCESS );
    }

    // ------------------------------------------------------------------------

    /**
     * App::optionVersion
     *
     * @return void
     */
    final public function optionVersion()
    {
        if ( property_exists( $this, 'version' ) ) {
            if ( ! empty( $this->version ) ) {
                output()->write(
                    ( new Format() )
                        ->setString( $this->name . ' v' . $this->version . ' Copyright (c) 2011 - ' . date( 'Y' ) . ' Steeve Andrian Salim' )
                        ->setNewLinesAfter( 1 )
                );

                output()->write(
                    ( new Format() )
                        ->setIndent( 2 )
                        ->setString( 'this framework is trademark of Steeve Andrian Salim' )
                        ->setNewLinesAfter( 1 )
                );
            }
        }
    }

    public function optionForm()
    {
        $form = new Form();
        $form->text( 'full_name', 'What is your name?', true );
        $form->confirm( 'sure', 'Are you sure?', true );
        $form->options( 'choose', 'What is your favorite color', [ 'red' => 'Red', 'green' => 'Green' ], true );

        print_out( input()->post() );
    }

    public function optionProgress()
    {
        // example

        $tasks = rand() % 700 + 600;
        $done = 0;

        $progress = new ProgressBar();

        for ( $done = 0; $done <= $tasks; $done++ ) {
            usleep( ( rand() % 127 ) * 100 );
            $progress->update( $done, $tasks );
        }
    }
}