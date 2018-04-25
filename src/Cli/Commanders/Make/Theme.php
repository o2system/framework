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

namespace O2System\Framework\Cli\Commanders\Make;

// ------------------------------------------------------------------------

use O2System\Framework\Cli\Commanders\Make;
use O2System\Kernel\Cli\Writers\Format;

/**
 * Class Theme
 *
 * @package O2System\Framework\Cli\Commanders\Make
 */
class Theme extends Make
{
    /**
     * Module::$commandDescription
     *
     * Command description.
     *
     * @var string
     */
    protected $commandDescription = 'CLI_MAKE_THEME_DESC';

    public function execute()
    {
        parent::execute();

        if ( empty( $this->optionFilename ) ) {
            output()->write(
                ( new Format() )
                    ->setContextualClass( Format::DANGER )
                    ->setString( language()->getLine( 'CLI_MAKE_THEME_E_NAME' ) )
                    ->setNewLinesAfter( 1 )
            );

            exit( EXIT_ERROR );
        }

        $themePath = $this->optionPath . $this->optionFilename . DIRECTORY_SEPARATOR;

        if ( ! is_dir( $themePath ) ) {
            mkdir( $themePath, 777, true );
        } else {
            output()->write(
                ( new Format() )
                    ->setContextualClass( Format::DANGER )
                    ->setString( language()->getLine( 'CLI_MAKE_THEME_E_EXISTS', [ $themePath ] ) )
                    ->setNewLinesAfter( 1 )
            );

            exit( EXIT_ERROR );
        }

        $jsProps[ 'name' ] = readable(
            pathinfo( $themePath, PATHINFO_FILENAME ),
            true
        );

        if ( empty( $this->namespace ) ) {
            @list( $themeDirectory, $themeName ) = explode( $themeType, dirname( $themePath ) );
            $namespace = loader()->getDirNamespace( $themeDirectory ) .
                $themeType . '\\' . prepare_class_name(
                    $this->optionFilename
                ) . '\\';
        } else {
            $namespace = prepare_class_name( $this->namespace );
            $jsProps[ 'namespace' ] = rtrim( $namespace, '\\' ) . '\\';
        }

        $jsProps[ 'created' ] = date( 'd M Y' );

        loader()->addNamespace( $namespace, $themePath );

        $fileContent = json_encode( $jsProps, JSON_PRETTY_PRINT );

        $filePath = $themePath . 'theme.jsprop';

        file_put_contents( $filePath, $fileContent );

        $this->optionPath = $themePath;
        $this->optionFilename = prepare_filename( $this->optionFilename ) . '.php';

        ( new Controller() )
            ->optionPath( $this->optionPath )
            ->optionFilename( $this->optionFilename );

        if ( is_dir( $themePath ) ) {
            output()->write(
                ( new Format() )
                    ->setContextualClass( Format::SUCCESS )
                    ->setString( language()->getLine( 'CLI_MAKE_THEME_S_MAKE', [ $themePath ] ) )
                    ->setNewLinesAfter( 1 )
            );

            exit( EXIT_SUCCESS );
        }
    }
}