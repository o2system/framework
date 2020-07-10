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
 * Class Library
 *
 * @package O2System\Framework\Cli\Commanders\Make
 */
class Library extends Make
{
    /**
     * Library::$commandDescription
     *
     * Command description.
     *
     * @var string
     */
    protected $commandDescription = 'CLI_MAKE_LIBRARY_DESC';

    // ------------------------------------------------------------------------

    /**
     * Library::execute
     */
    public function execute()
    {
        parent::execute();

        if (empty($this->optionFilename)) {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::DANGER)
                    ->setString(language()->getLine('CLI_MAKE_LIBRARY_E_FILENAME'))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_ERROR);
        }

        if (strpos($this->optionPath, 'Libraries') === false) {
            $filePath = $this->optionPath . 'Libraries' . DIRECTORY_SEPARATOR . $this->optionFilename;
        } else {
            $filePath = $this->optionPath . $this->optionFilename;
        }

        $fileDirectory = dirname($filePath) . DIRECTORY_SEPARATOR;

        if ( ! is_dir($fileDirectory)) {
            mkdir($fileDirectory, 0777, true);
        }

        if (is_file($filePath)) {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::DANGER)
                    ->setString(language()->getLine('CLI_MAKE_LIBRARY_E_EXISTS', [$filePath]))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_ERROR);
        }

        $className = prepare_class_name(pathinfo($filePath, PATHINFO_FILENAME));
        @list($namespaceDirectory, $subNamespace) = explode('Libraries', str_replace(['\\','/'], '\\', dirname($filePath)));
        $subNamespace = rtrim($subNamespace, '\\');

        $classNamespace = loader()->getDirNamespace(
                $namespaceDirectory
            ) . 'Libraries' . (empty($subNamespace)
                ? null
                : $subNamespace) . '\\';

        $vars[ 'CREATE_DATETIME' ] = date('d/m/Y H:m');
        $vars[ 'NAMESPACE' ] = trim($classNamespace, '\\');
        $vars[ 'PACKAGE' ] = '\\' . trim($classNamespace, '\\');
        $vars[ 'CLASS' ] = $className;
        $vars[ 'FILEPATH' ] = $filePath;

        $phpTemplateFilePaths = $this->getFilePaths(true);

        foreach($phpTemplateFilePaths as $phpTemplateFilePath)
        {
            if(is_file($phpTemplateFilePath . 'Library.tpl')) {
                $phpTemplate = file_get_contents($phpTemplateFilePath . 'Library.tpl');
                break;
            }
        }

        $fileContent = str_replace(array_keys($vars), array_values($vars), $phpTemplate);
        file_put_contents($filePath, $fileContent);

        if (is_file($filePath)) {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::SUCCESS)
                    ->setString(language()->getLine('CLI_MAKE_LIBRARY_S_MAKE', [$filePath]))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_SUCCESS);
        }
    }
}