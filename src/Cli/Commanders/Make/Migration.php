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
 * Class Migration
 *
 * @package O2System\Framework\Cli\Commanders\Make
 */
class Migration extends Make
{
    /**
     * Migration::$commandDescription
     *
     * Command description.
     *
     * @var string
     */
    protected $commandDescription = 'CLI_MAKE_MIGRATION_DESC';

    /**
     * Migration::$optionFileVersion
     *
     * @var string
     */
    protected $optionFileVersion = 'v.0.0.0';

    /**
     * Migration::$optionNoSql
     *
     * @var bool
     */
    protected $optionNoSql = false;

    // ------------------------------------------------------------------------

    /**
     * Migration::optionFileVersion
     */
    public function optionFileVersion($version)
    {
        $this->optionFileVersion = $version;
    }

    // ------------------------------------------------------------------------

    /**
     * Migration::optionPath
     *
     * @param string $path
     */
    public function optionPath($path)
    {
        $this->optionPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }

    // ------------------------------------------------------------------------

    /**
     * Migration::optionNoSql
     */
    public function optionNoSql()
    {
        $this->optionNoSql = true;
    }

    // ------------------------------------------------------------------------

    /**
     * Migration::execute
     */
    public function execute()
    {
        parent::execute();

        if (empty($this->optionFilename)) {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::DANGER)
                    ->setString(language()->getLine('CLI_MAKE_MIGRATION_E_FILENAME'))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_ERROR);
        }

        $className = studlycase(pathinfo($this->optionFilename, PATHINFO_FILENAME));

        if(empty($this->optionFileVersion)) {
            $filename = date('YmdHis') . '_' . underscore($this->optionFilename);
        } else {
            $filename = $this->optionFileVersion . '_' . underscore($this->optionFilename);
        }

        $filePath = PATH_DATABASE . 'migrations' . DIRECTORY_SEPARATOR;

        if( ! empty($this->optionPath) ) {
            $filePath = $filePath . $this->optionPath;
        }

        $filePath = $filePath . $filename;

        $fileDirectory = dirname($filePath) . DIRECTORY_SEPARATOR;

        if ( ! is_dir($fileDirectory)) {
            mkdir($fileDirectory, 0777, true);
        }

        if (is_file($filePath)) {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::DANGER)
                    ->setString(language()->getLine('CLI_MAKE_MIGRATION_E_EXISTS', [$filePath]))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_ERROR);
        }

        $vars[ 'CREATE_DATETIME' ] = date('d/m/Y H:m');
        $vars[ 'BASE_MIGRATION' ] = 'O2System\Framework\Models\Sql\Migration';

        if($this->optionNoSql) {
            $vars[ 'BASE_MIGRATION' ] = 'O2System\Framework\Models\NoSql\Migration';
        }

        $vars[ 'CLASS' ] = $className;
        $vars[ 'FILEPATH' ] = $filePath;

        $phpTemplateFilePaths = $this->getFilePaths(true);

        foreach($phpTemplateFilePaths as $phpTemplateFilePath)
        {
            if(is_file($phpTemplateFilePath . 'Migration.tpl')) {
                $phpTemplate = file_get_contents($phpTemplateFilePath . 'Migration.tpl');
                break;
            }
        }

        $fileContent = str_replace(array_keys($vars), array_values($vars), $phpTemplate);
        file_put_contents($filePath, $fileContent);

        if (is_file($filePath)) {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::SUCCESS)
                    ->setString(language()->getLine('CLI_MAKE_MIGRATION_S_MAKE', [$filePath]))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_SUCCESS);
        }
    }
}