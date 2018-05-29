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
 * Class Widget
 *
 * @package O2System\Framework\Cli\Commanders\Make
 */
class Widget extends Make
{
    /**
     * Widget::$commandDescription
     *
     * Command description.
     *
     * @var string
     */
    protected $commandDescription = 'CLI_MAKE_WIDGET_DESC';

    public function execute()
    {
        parent::execute();

        if (empty($this->optionFilename)) {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::DANGER)
                    ->setString(language()->getLine('CLI_MAKE_WIDGET_E_NAME'))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_ERROR);
        }

        if (strpos($this->optionPath, 'Widgets') === false) {
            $widgetPath = $this->optionPath . 'Widgets' . DIRECTORY_SEPARATOR . $this->optionFilename . DIRECTORY_SEPARATOR;
        } else {
            $widgetPath = $this->optionPath . $this->optionFilename . DIRECTORY_SEPARATOR;
        }

        if ( ! is_dir($widgetPath)) {
            mkdir($widgetPath, 0777, true);
        } else {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::DANGER)
                    ->setString(language()->getLine('CLI_MAKE_WIDGET_E_EXISTS', [$widgetPath]))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_ERROR);
        }

        $jsProps[ 'name' ] = readable(
            pathinfo($widgetPath, PATHINFO_FILENAME),
            true
        );

        if (empty($this->namespace)) {
            @list($moduleDirectory, $moduleName) = explode('Widgets', dirname($widgetPath));
            $namespace = loader()->getDirNamespace($moduleDirectory) .
                'Widgets' . '\\' . prepare_class_name(
                    $this->optionFilename
                ) . '\\';
        } else {
            $namespace = prepare_class_name($this->namespace);
            $jsProps[ 'namespace' ] = rtrim($namespace, '\\') . '\\';
        }

        $jsProps[ 'created' ] = date('d M Y');

        loader()->addNamespace($namespace, $widgetPath);

        $fileContent = json_encode($jsProps, JSON_PRETTY_PRINT);

        $filePath = $widgetPath . 'widget.jsprop';

        file_put_contents($filePath, $fileContent);

        $this->optionPath = $widgetPath;
        $this->optionFilename = prepare_filename($this->optionFilename) . '.php';

        (new Presenter())
            ->optionPath($this->optionPath)
            ->optionFilename($this->optionFilename);

        if (is_dir($widgetPath)) {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::SUCCESS)
                    ->setString(language()->getLine('CLI_MAKE_WIDGET_S_MAKE', [$widgetPath]))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_SUCCESS);
        }
    }
}