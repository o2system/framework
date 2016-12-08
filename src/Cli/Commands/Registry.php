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

namespace O2System\Framework\Cli\Commands;

// ------------------------------------------------------------------------

use O2System\Cli\Writers\Formatter;
use O2System\Cli\Writers\Table;
use O2System\Framework\Abstracts\AbstractCommand;

/**
 * Class Registry
 *
 * @package O2System\Framework\Cli\Commands
 */
class Registry extends AbstractCommand
{
    protected $version = '1.0.0';

    /**
     * Registry::$name
     *
     * Command name.
     *
     * @var string
     */
    protected $caller = 'registry';

    /**
     * Registry::$description
     *
     * Command description.
     *
     * @var string
     */
    protected $description = 'Registry management console';

    /**
     * Registry::$options
     *
     * Command options.
     *
     * @var array
     */
    protected $options = [
        'fetch'  => [
            'description' => 'Fetch applications registry',
        ],
        'update' => [
            'description' => 'Update applications registry',
        ],
        'flush'  => [
            'description' => 'Flush applications registry',
        ],
        'info'   => [
            'description' => 'Applications registry info',
        ],
    ];

    public function optionFetch ( $type = null )
    {
        if ( in_array( $type, [ 'modules', 'languages' ] ) ) {
            switch ( $type ) {
                case 'modules':
                    $line = PHP_EOL . print_r( modules()->fetchRegistry(), true );
                    break;

                case 'languages':
                    $line = PHP_EOL . print_r( language()->fetchRegistry(), true );
                    break;
            }
        } else {
            $output[ 'languages' ] = language()->fetchRegistry();
            $output[ 'modules' ] = modules()->fetchRegistry();

            $line = PHP_EOL . print_r( $output, true );
        }

        if ( isset( $line ) ) {
            output()->writeln( $line );

            exit( EXIT_SUCCESS );
        }
    }

    public function optionUpdate ( $type = null )
    {
        if ( in_array( $type, [ 'modules', 'languages' ] ) ) {
            switch ( $type ) {
                case 'modules':
                    modules()->updateRegistry();
                    break;

                case 'languages':
                    language()->updateRegistry();
                    break;
            }
        } else {
            modules()->updateRegistry();
            language()->updateRegistry();
        }

        exit( EXIT_SUCCESS );
    }

    public function optionFlush ( $type = null )
    {
        if ( in_array( $type, [ 'modules', 'languages' ] ) ) {
            switch ( $type ) {
                case 'modules':
                    modules()->flushRegistry();
                    break;

                case 'languages':
                    language()->flushRegistry();
                    break;
            }

        } else {
            modules()->flushRegistry();
            language()->flushRegistry();
        }

        exit( EXIT_SUCCESS );
    }

    public function optionInfo ()
    {
        $formatter = new Formatter();

        $table = new Table();

        output()->writeln( PHP_EOL );

        $table
            ->addHeader( 'Metadata' )
            ->addHeader( 'Total' );

        $table
            ->addRow()
            ->addColumn( 'Modules' )
            ->addColumn( modules()->countRegistry() );

        $table
            ->addRow()
            ->addColumn( 'Language' )
            ->addColumn( language()->countRegistry() );

        output()->writeln(
            $formatter
                ->setIndent( 1 )
                ->format( PHP_EOL . $table->render() )
        );

        exit( EXIT_SUCCESS );
    }

    public function optionMetadata ( $metadata )
    {
        if ( in_array( $metadata, [ 'modules', 'languages' ] ) ) {
            switch ( $metadata ) {
                case 'modules':
                    $line = PHP_EOL . print_r( modules()->getRegistry(), true );
                    break;

                case 'languages':
                    $line = PHP_EOL . print_r( language()->getRegistry(), true );
                    break;
            }

            if ( isset( $line ) ) {
                output()->writeln( $line );

                exit( EXIT_SUCCESS );
            }
        }
    }

    protected function execute ()
    {

    }
}