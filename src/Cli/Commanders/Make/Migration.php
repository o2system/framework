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
        $this->__callOptions();

        if (empty($this->optionFilename)) {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::DANGER)
                    ->setString(language()->getLine('CLI_MAKE_MIGRATION_E_FILENAME'))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_ERROR);
        }

        $className = studlycase($this->optionFilename);

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

        $phpTemplate = <<<PHPTEMPLATE
<?php
/**
 * Created by O2System Framework File Generator.
 * DateTime: CREATE_DATETIME
 */

// ------------------------------------------------------------------------

use BASE_MIGRATION

/**
 * Class CLASS
 */
class CLASS extends Migration
{
    /**
     * CLASS::up
     */
    public function up()
    {
        // TODO: Change the autogenerated stub
    }
    
    // ------------------------------------------------------------------------
    
    /**
     * CLASS::down
     */
    public function down()
    {
        // TODO: Change the autogenerated stub
    }
}
PHPTEMPLATE;

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