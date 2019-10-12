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

    // ------------------------------------------------------------------------

    /**
     * Widget::execute
     */
    public function execute()
    {
        $this->__callOptions();

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

        $jsonProperties[ 'name' ] = readable(
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
            $jsonProperties[ 'namespace' ] = rtrim($namespace, '\\') . '\\';
        }

        $jsonProperties[ 'created' ] = date('d M Y');

        loader()->addNamespace($namespace, $widgetPath);

        $fileContent = json_encode($jsonProperties, JSON_PRETTY_PRINT);

        $filePath = $widgetPath . 'widget.json';

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