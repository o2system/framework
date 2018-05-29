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

use O2System\Kernel\Cli\Commander;
use O2System\Kernel\Cli\Writers\Format;

/**
 * Class Make
 *
 * @package O2System\Framework\Cli\Commanders
 */
class Serve extends Commander
{
    /**
     * Make::$commandVersion
     *
     * Command version.
     *
     * @var string
     */
    protected $commandVersion = '1.0.0';

    /**
     * Make::$commandDescription
     *
     * Command description.
     *
     * @var string
     */
    protected $commandDescription = 'CLI_SERVE_DESC';

    /**
     * Make::$commandOptions
     *
     * Command options.
     *
     * @var array
     */
    protected $commandOptions = [
        'host' => [
            'description' => 'CLI_SERVE_HOST_HELP',
            'required'    => false,
        ],
        'port' => [
            'description' => 'CLI_SERVE_PORT_HELP',
            'required'    => false,
        ],
    ];

    /**
     * Serve::$host
     *
     * Serve host.
     *
     * @var string
     */
    protected $optionHost;

    /**
     * Serve::$port
     *
     * Serve port.
     *
     * @var int
     */
    protected $optionPort;

    public function optionHost($host)
    {
        $this->optionHost = $host;
    }

    public function optionPort($port)
    {
        $this->optionPort = $port;
    }

    public function execute()
    {
        $options = input()->get();

        if (empty($options)) {
            $_GET[ 'host' ] = 'localhost';
            $_GET[ 'port' ] = 8000;
        }

        parent::execute();

        output()->write(
            (new Format())
                ->setContextualClass(Format::SUCCESS)
                ->setString(language()->getLine('CLI_SERVE_STARTED', [$this->optionHost, $this->optionPort]))
                ->setNewLinesAfter(1)
        );

        $_SERVER[ 'DOCUMENT_ROOT' ] = PATH_PUBLIC;

        output()->write(
            (new Format())
                ->setContextualClass(Format::INFO)
                ->setString(language()->getLine('CLI_SERVE_DOC_ROOT', [$_SERVER[ 'DOCUMENT_ROOT' ]]))
                ->setNewLinesAfter(1)
        );

        output()->write(
            (new Format())
                ->setContextualClass(Format::WARNING)
                ->setString(language()->getLine('CLI_SERVE_STOP'))
                ->setNewLinesAfter(1)
        );

        /*
         * Call PHP's built-in webserver, making sure to set our
         * base path to the public folder, and to use the rewrite file
         * to ensure our environment is set and it simulates basic mod_rewrite.
         */
        passthru('php -S ' .
            $this->optionHost .
            ':' .
            $this->optionPort .
            ' -t ' .
            str_replace('\\', DIRECTORY_SEPARATOR, DIR_PUBLIC) . ' ' . PATH_ROOT . 'server.php'
        );
    }
}