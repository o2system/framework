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

namespace O2System\Framework\Cli\Commanders;

// ------------------------------------------------------------------------

use O2System\Kernel\Cli\Writers\Format;
use O2System\Framework\Cli\Commander;

/**
 * Class Build
 * @package O2System\Framework\Cli\Commanders
 */
class Build extends Commander
{
    /**
     * Build::$commandVersion
     *
     * Command version.
     *
     * @var string
     */
    protected $commandVersion = '1.0.0';

    /**
     * Build::$commandDescription
     *
     * Command description.
     *
     * @var string
     */
    protected $commandDescription = 'CLI_BUILD_DESC';

    /**
     * Build::$commandOptions
     *
     * Command options.
     *
     * @var array
     */
    protected $commandOptions = [
        'filename' => [
            'description' => 'CLI_BUILD_FILENAME_HELP',
            'required'    => false,
        ],
        'main'     => [
            'description' => 'CLI_BUILD_MAIN_HELP',
            'required'    => false,
        ],
        'force'    => [
            'description' => 'CLI_BUILD_FORCE_HELP',
            'required'    => false,
        ],
    ];

    /**
     * Build::$optionFilename
     *
     * Build filename.
     *
     * @var string
     */
    protected $optionFilename;

    /**
     * Build::$optionMain
     *
     * Build main script.
     *
     * @var string
     */
    protected $optionMain;

    /**
     * Build::optionFilename
     *
     * @param string $name
     */
    public function optionFilename($filename)
    {
        $this->optionFilename = str_replace('.phar', '', $filename) . '.phar';
    }

    // ------------------------------------------------------------------------

    /**
     * Build::optionMain
     *
     * @param string $name
     */
    public function optionMain($main)
    {
        $this->optionMain = $main;
    }

    // ------------------------------------------------------------------------

    /**
     * Build::execute
     *
     * @throws \ReflectionException
     */
    public function execute()
    {
        $this->__callOptions();

        $filename = empty($this->optionFilename) ? 'app.phar' : $this->optionFilename;
        $filename = str_replace('.phar', '', $filename);
        $filePath = PATH_ROOT . 'build' . DIRECTORY_SEPARATOR . $filename;

        if (ini_get('phar.readonly') == 1 and $this->optionForce === false) {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::DANGER)
                    ->setString(language()->getLine('CLI_BUILD_PHAR_READONLY'))
                    ->setNewLinesAfter(1)
            );
            exit(EXIT_ERROR);
        }

        output()->verbose(
            (new Format())
                ->setContextualClass(Format::INFO)
                ->setString(language()->getLine('CLI_BUILD_START'))
                ->setNewLinesAfter(1)
        );

        // Remove build directory
        $fileDirectory = dirname($filePath) . DIRECTORY_SEPARATOR;
        if (is_dir($fileDirectory)) {
            $directoryHandle = opendir($fileDirectory);
            if ( ! $directoryHandle) {
                return false;
            }
            while ($file = readdir($directoryHandle)) {
                if ($file != '.' && $file != '..') {
                    if (is_file($fileDirectory . $file)) {
                        unlink($fileDirectory . $file);
                    }
                }
            }
            closedir($directoryHandle);
            rmdir($fileDirectory);
        }

        if ( ! is_writable(dirname($filePath))) {
            @mkdir(dirname($filePath), 0777, true);

            output()->verbose(
                (new Format())
                    ->setContextualClass(Format::INFO)
                    ->setString(language()->getLine('CLI_BUILD_MAKE_DIRECTORY'))
                    ->setNewLinesAfter(1)
            );
        }

        try {
            $pharData = new \PharData($filePath . '.tar');
            $phar = $pharData->convertToExecutable(\Phar::PHAR);

            // Build from PATH_ROOT using Recursive Directory Iterator
            $phar->buildFromIterator(new \RecursiveIteratorIterator(new \RecursiveCallbackFilterIterator(
                new \RecursiveDirectoryIterator(PATH_ROOT,
                    \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS),
                function ($current, $key, $iterator) {
                    if ($current->isDir()) {
                        // exclude build directory
                        if ($current->getFilename() === 'build') {
                            return false;
                        }
                    }

                    return true;
                })), PATH_ROOT);

            // Define main script
            $main = 'public/index.php';
            if (empty($this->optionMain)) {
                // Default Carbon Boilerplate Detection
                if (is_file(PATH_APP . 'console')) {
                    $main = 'app/console';

                    output()->verbose(
                        (new Format())
                            ->setContextualClass(Format::WARNING)
                            ->setString(language()->getLine('CLI_BUILD_USE_CARBON_DEFAULT'))
                            ->setNewLinesAfter(1)
                    );
                }
            } else {
                $main = $this->optionMain;
            }

            $phar->setStub($phar->createDefaultStub($main));

            output()->verbose(
                (new Format())
                    ->setContextualClass(Format::INFO)
                    ->setString(language()->getLine('CLI_BUILD_START_GZ_COMPRESSION'))
                    ->setNewLinesAfter(1)
            );

            $pharData->convertToExecutable(\Phar::TAR, \Phar::GZ, '.phar.tgz');

            output()->write(
                (new Format())
                    ->setContextualClass(Format::SUCCESS)
                    ->setString(language()->getLine('CLI_BUILD_SUCCESSFUL'))
                    ->setNewLinesAfter(1)
            );
        } catch (\Exception $exception) {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::DANGER)
                    ->setString($exception->getMessage())
                    ->setNewLinesAfter(1)
            );
        }
    }
}