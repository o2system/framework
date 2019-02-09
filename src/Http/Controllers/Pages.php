<?php
/**
 * This file is part of the O2System Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */

// ------------------------------------------------------------------------

namespace O2System\Framework\Http\Controllers;

// ------------------------------------------------------------------------

use O2System\Framework\Http\Controller;
use O2System\Framework\Http\Router\DataStructures\Page;

/**
 * Class Pages
 *
 * @package O2System\Framework\Http\Controllers
 */
class Pages extends Controller
{
    /**
     * Pages Page
     *
     * @var Page
     */
    protected $page;

    // ------------------------------------------------------------------------

    /**
     * Pages::setPage
     *
     * @param \O2System\Framework\Http\Router\DataStructures\Page $page
     *
     * @return void
     */
    public function setPage(Page $page)
    {
        $this->page = $page;
    }

    // ------------------------------------------------------------------------

    /**
     * Pages::index
     *
     * @return void
     */
    public function index()
    {
        if (false !== ($presets = $this->page->getPresets())) {
            if ($presets->offsetExists('theme')) {
                $this->presenter->theme->set($presets->theme);
            }

            if ($presets->offsetExists('layout')) {
                $this->presenter->theme->setLayout($presets->layout);
            }

            if ($presets->offsetExists('title')) {
                $this->presenter->meta->title->append($presets->title);
            }

            if ($presets->offsetExists('pageTitle')) {
                $this->presenter->meta->title->append($presets->pageTitle);
            }

            if ($presets->offsetExists('browserTitle')) {
                $this->presenter->meta->title->replace($presets->browserTitle);
            }
        }

        $this->view->page($this->page);
    }
}