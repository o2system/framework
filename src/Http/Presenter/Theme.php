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

/**
 * Class Theme
 *
 * @package O2System\Framework\Http\Presenter
 */
class Theme
{
    public $use = false;
    public $active;

    public function load( $theme )
    {
        $this->active = modules()->current()->getTheme( $theme );

        if ( $this->active instanceof \O2System\Framework\Datastructures\Module\Theme ) {

            if ( ! defined( 'PATH_THEME' ) ) {
                define( 'PATH_THEME', $this->active->getRealPath() );
            }

            // add theme public directory
            loader()->addPublicDir( $this->active->getRealPath() . 'assets' );

            if ( $this->active->getConfig()->offsetExists( 'assets' ) ) {
                presenter()->assets->autoload( $this->active->getConfig()->offsetGet( 'assets' ) );
            }

            presenter()->assets->autoload(
                [
                    'css' => [ 'theme', 'custom' ],
                    'js'  => [ 'theme', 'custom' ],
                ]
            );

            // Load theme layout
            $this->active->setLayout( 'theme' );
            if ( $this->active->setLayout( 'theme' ) ) {
                $partials = $this->active->getLayout()->getPartials()->getArrayCopy();

                foreach ( $partials as $offset => $partial ) {
                    presenter()->partials->addPartial( $offset, $partial->getPathName() );
                }
            }

            $this->use = true;

            return true;
        }

        return false;
    }
}