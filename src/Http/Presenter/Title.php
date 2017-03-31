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
 * Class Title
 *
 * @package O2System\Framework\Http\Presenter\Models
 */
class Title
{
    protected $browser;

    protected $page;

    public function __construct()
    {
        $this->browser = new Title\Browser();
        $this->page = new Title\Page();
    }

    public function &__get( $property )
    {
        $get[ $property ] = null;

        if ( property_exists( $this, $property ) ) {
            return $this->{$property};
        }

        return $get[ $property ];
    }

    public function setSeparator( $separator )
    {
        $this->page->setSeparator( $separator );
        $this->browser->setSeparator( $separator );

        return $this;
    }

    // ------------------------------------------------------------------------

    public function set( $title )
    {
        $this->setPageTitle( $title );
        $this->setBrowserTitle( $title );

        return $this;
    }

    // ------------------------------------------------------------------------

    public function setPageTitle( $title )
    {
        $this->page = new Title\Page();
        $this->page[] = $title;

        return $this->page;
    }

    // ------------------------------------------------------------------------

    public function setBrowserTitle( $title )
    {
        $this->browser = new Title\Browser();
        $this->browser[] = $title;

        return $this->browser;
    }

    // ------------------------------------------------------------------------

    /**
     * add
     *
     * @param $title
     *
     * @return $this
     */
    public function add( $title )
    {
        $this->page[] = $title;
        $this->browser[] = $title;

        return $this;
    }

    // ------------------------------------------------------------------------

    public function addTitlePage( $title )
    {
        $this->page[] = $title;

        return $this->page;
    }

    public function addTitleBrowser( $title )
    {
        $this->browser[] = $title;

        return $this->browser;
    }
}