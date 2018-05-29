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

use O2System\Spl\Info\SplFileInfo;

/**
 * Class Theme
 *
 * @package O2System\Framework\Http\Presenter
 */
class Theme
{
    public $use = false;

    /**
     * @var \O2System\Framework\Datastructures\Module\Theme
     */
    public $active;

    public function set($theme)
    {
        if (($this->active = modules()->current()->getTheme($theme)) instanceof \O2System\Framework\Datastructures\Module\Theme) {

            if ( ! defined('PATH_THEME')) {
                define('PATH_THEME', $this->active->getRealPath());
            }

            // add theme view directory
            view()->addFilePath($this->active->getRealPath());

            // add theme output directory
            output()->setFileDirName('views'); // replace views folder base on theme structure
            output()->addFilePath($this->active->getRealPath(), 'theme');
            output()->setFileDirName('Views'); // restore Views folder base on PSR-4 folder structure

            // add theme public directory
            loader()->addPublicDir($this->active->getRealPath() . 'assets' . DIRECTORY_SEPARATOR, 'theme');

            // set theme layout
            $this->setLayout('theme');

            $this->use = true;

            return true;
        }

        $this->use = false;
        $this->active = false;

        return false;
    }

    public function setLayout($layout)
    {
        $this->active->setLayout($layout);

        if (false !== ($layout = $this->active->getLayout($layout))) {

            $layoutDirectory = dirname($layout->getRealPath()) . DIRECTORY_SEPARATOR;

            // add theme layout output directory
            output()->setFileDirName('views'); // replace views folder base on layout structure
            output()->addFilePath($layoutDirectory, 'layout');
            output()->setFileDirName('Views'); // restore Views folder base on PSR-4 folder structure

            // add theme layout public directory
            loader()->addPublicDir($layoutDirectory . 'assets' . DIRECTORY_SEPARATOR, 'layout');

            $partials = $layout->getPartials()->getArrayCopy();

            foreach ($partials as $offset => $partial) {
                if ($partial instanceof SplFileInfo) {
                    presenter()->partials->addPartial($offset, $partial->getPathName());
                }
            }

            return true;
        }

        return false;
    }

    public function load()
    {
        if ($this->active instanceof \O2System\Framework\Datastructures\Module\Theme) {

            if ($this->active->getConfig()->offsetExists('assets')) {
                presenter()->assets->autoload($this->active->getConfig()->offsetGet('assets'));
            }

            presenter()->assets->loadFiles(
                [
                    'css' => ['theme', 'custom'],
                    'js'  => ['theme', 'custom'],
                ]
            );

            if (false !== ($layout = $this->active->getLayout())) {
                presenter()->assets->loadFiles(
                    [
                        'css' => ['layout', 'custom'],
                        'js'  => ['layout', 'custom'],
                    ]
                );
            }

            return true;
        }

        return false;
    }
}