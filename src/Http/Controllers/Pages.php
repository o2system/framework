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
                presenter()->theme->set($presets->theme);
            }

            if ($presets->offsetExists('layout')) {
                presenter()->theme->setLayout($presets->layout);
            }

            if ($presets->offsetExists('title')) {
                presenter()->meta->title->append($presets->title);
            }

            if ($presets->offsetExists('pageTitle')) {
                presenter()->meta->title->append($presets->pageTitle);
            }

            if ($presets->offsetExists('browserTitle')) {
                presenter()->meta->title->replace($presets->browserTitle);
            }
        }

        view()->page($this->page);
    }
}