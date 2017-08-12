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

namespace O2System\Framework\Http\Presenter;

// ------------------------------------------------------------------------

use O2System\Psr\Patterns\AbstractItemStoragePattern;

/**
 * Class Widgets
 *
 * @package O2System\Framework\Http\Presenter
 */
class Widgets extends AbstractItemStoragePattern
{
    public function hasWidget( $widgetOffset )
    {
        return $this->__isset( $widgetOffset );
    }

    public function addWidget( $widgetOffset )
    {
        $widgetClassName = modules()->current()->getNamespace() . 'Widgets\\' . studlycase( $widgetOffset ) . '\\Widget';
        $widgetFilepath = modules()->current()->getRealPath() . 'Widgets' . DIRECTORY_SEPARATOR . studlycase( $widgetOffset ) . DIRECTORY_SEPARATOR . 'Widget.php';

        if ( is_file( $widgetFilepath ) ) {
            $this->store( camelcase( $widgetOffset ), $widgetClassName );
        }
    }

    public function __get( $widget )
    {
        $widgetClassName = parent::__get( $widget );

        if ( class_exists( $widgetClassName ) ) {
            $widgetPresenter = new $widgetClassName();

            $widgetViewFilePath = str_replace(
                'Widget.php',
                'view.phtml',
                $widgetPresenter->getClassInfo()->getFileInfo()->getRealPath()
            );

            if ( presenter()->theme->use === true ) {
                $widgetViewReplacementPath = presenter()->theme->active->getPathName()
                    . DIRECTORY_SEPARATOR
                    . 'views'
                    . DIRECTORY_SEPARATOR
                    . strtolower(
                        str_replace( [ PATH_APP, DIRECTORY_SEPARATOR . 'view.phtml' ], '', $widgetViewFilePath )
                    );

                $viewsFileExtensions = [
                    '.php',
                    '.phtml',
                ];

                // Add Theme File Extensions
                if ( presenter()->theme->active->getConfig()->offsetExists( 'extension' ) ) {
                    array_unshift( $viewsFileExtensions,
                        presenter()->theme->active->getConfig()->offsetGet( 'extension' ) );
                } elseif ( presenter()->theme->active->getConfig()->offsetExists( 'extensions' ) ) {
                    $viewsFileExtensions = array_merge(
                        presenter()->theme->active->getConfig()->offsetGet( 'extensions' ),
                        $viewsFileExtensions
                    );
                }

                foreach( $viewsFileExtensions as $viewsFileExtension ) {
                    if( is_file( $widgetViewReplacementPath . $viewsFileExtension ) ) {
                        $widgetViewFilePath = $widgetViewReplacementPath . $viewsFileExtension;
                    }
                }

            }

            if ( is_file( $widgetViewFilePath ) ) {
                parser()->loadVars( $widgetPresenter->getArrayCopy() );
                parser()->loadFile( $widgetViewFilePath );

                return parser()->parse();
            } elseif ( method_exists( $widgetPresenter, 'render' ) ) {
                return $widgetPresenter->render();
            }
        }

        return null;
    }
}