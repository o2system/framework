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

namespace O2System\Framework\Http\Presenter\Repositories;

// ------------------------------------------------------------------------

use O2System\Spl\Patterns\Structural\Repository\AbstractRepository;

/**
 * Class Widgets
 *
 * @package O2System\Framework\Http\Presenter\Repositories
 */
class Widgets extends AbstractRepository
{
    /**
     * Widgets::hasWidget
     *
     * @param string $widgetOffset
     *
     * @return bool
     */
    public function hasWidget($widgetOffset)
    {
        return $this->__isset($widgetOffset);
    }

    // ------------------------------------------------------------------------

    /**
     * Widgets::load
     *
     * @param string $widgetOffset
     *
     * @return bool
     */
    public function load($widgetOffset)
    {
        $widgetDirectory = modules()->top()->getRealPath() . 'Widgets' . DIRECTORY_SEPARATOR . studlycase($widgetOffset) . DIRECTORY_SEPARATOR;

        if (is_dir($widgetDirectory)) {
            $widget = new DataStructures\Module\Widget($widgetDirectory);
            $this->store(camelcase($widgetOffset), $widget);
        }

        return $this->exists($widgetOffset);
    }

    // ------------------------------------------------------------------------

    /**
     * Widgets::get
     *
     * @param string $offset
     *
     * @return string
     */
    public function get($offset)
    {
        if (null !== ($widget = parent::get($offset))) {

            $widgetViewFilePath = $widget->getRealPath() . 'Views' . DIRECTORY_SEPARATOR . $offset . '.phtml';

            if (presenter()->theme->use === true) {
                $widgetViewReplacementPath = str_replace(
                    $widget->getRealPath() . 'Views' . DIRECTORY_SEPARATOR,
                    presenter()->theme->active->getPathName() . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, [
                        'views',
                        'widgets',
                    ]) . DIRECTORY_SEPARATOR,
                    $widgetViewFilePath
                );

                $viewsFileExtensions = [
                    '.php',
                    '.phtml',
                ];

                // Add Theme File Extensions
                if (presenter()->theme->active->getPresets()->offsetExists('extension')) {
                    array_unshift($viewsFileExtensions,
                        presenter()->theme->active->getPresets()->offsetGet('extension'));
                } elseif (presenter()->theme->active->getPresets()->offsetExists('extensions')) {
                    $viewsFileExtensions = array_merge(
                        presenter()->theme->active->getPresets()->offsetGet('extensions'),
                        $viewsFileExtensions
                    );
                }

                foreach ($viewsFileExtensions as $viewsFileExtension) {
                    if (is_file($widgetViewReplacementPath . $viewsFileExtension)) {
                        $widgetViewFilePath = $widgetViewReplacementPath . $viewsFileExtension;
                    }
                }

            }

            loader()->addNamespace($widget->getNamespace(), $widget->getRealPath());
            $widgetPresenterClassName = $widgetPresenterClassName = $widget->getNamespace() . 'Presenters\\' . studlycase($offset);

            $widgetPresenter = new $widgetPresenterClassName();

            if (is_file($widgetViewFilePath)) {
                parser()->loadVars($widgetPresenter->getArrayCopy());
                parser()->loadFile($widgetViewFilePath);

                return parser()->parse();
            } elseif (method_exists($widgetPresenter, 'render')) {
                return $widgetPresenter->render();
            }
        }

        return null;
    }
}