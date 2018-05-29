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

namespace O2System\Framework\Http\Controllers;

// ------------------------------------------------------------------------

use O2System\Framework\Http\Controller;
use O2System\Framework\Http\Router\Datastructures\Page;

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
     * @param \O2System\Framework\Http\Router\Datastructures\Page $page
     *
     * @return void
     */
    public function setPage(Page $page)
    {
        $this->page = $page;

        if (false !== ($settings = $this->page->getSettings())) {
            if ($settings->offsetExists('theme')) {
                presenter()->theme->set($settings->theme);
            }

            if ($settings->offsetExists('layout')) {
                presenter()->theme->setLayout($settings->layout);
            }

            if ($settings->offsetExists('title')) {
                presenter()->meta->title->append($settings->title);
            }

            if ($settings->offsetExists('pageTitle')) {
                presenter()->meta->title->append($settings->pageTitle);
            }

            if ($settings->offsetExists('browserTitle')) {
                presenter()->meta->title->replace($settings->browserTitle);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Pages::index
     *
     * @return void
     */
    public function index()
    {
        view()->page($this->page);
    }
}