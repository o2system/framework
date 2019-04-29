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

namespace O2System\Framework\Http;

// ------------------------------------------------------------------------

use O2System\Gear\Trace;
use O2System\Spl\Exceptions\Abstracts\AbstractException;
use O2System\Spl\Exceptions\ErrorException;

/**
 * Class Output
 * @package O2System\Framework\Http
 */
class Output extends \O2System\Kernel\Http\Output
{
    /**
     * Output::__construct
     */
    public function __construct()
    {
        parent::__construct();

        if(services()->has('csrfProtection')) {
            $this->addHeader('X-CSRF-TOKEN', services()->get('csrfProtection')->getToken());
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Output::shutdownHandler
     *
     * Kernel defined shutdown handler function.
     *
     * @return void
     * @throws \O2System\Spl\Exceptions\ErrorException
     */
    public function shutdownHandler()
    {
        parent::shutdownHandler();

        // Execute Shutdown Service
        if (services()->has('shutdown')) {
            shutdown()->execute();
        }

    }

    // ------------------------------------------------------------------------

    /**
     * Output::getFilePath
     *
     * @param string $filename
     *
     * @return string
     */
    public function getFilePath($filename)
    {
        if (modules()) {
            $filePaths = modules()->getDirs('Views');
        } else {
            $filePaths = array_reverse($this->filePaths);
        }

        foreach ($filePaths as $filePath) {
            if (is_file($filePath . $filename . '.phtml')) {
                return $filePath . $filename . '.phtml';
                break;
            } elseif (is_file($filePath . 'errors' . DIRECTORY_SEPARATOR . $filename . '.phtml')) {
                return $filePath . 'errors' . DIRECTORY_SEPARATOR . $filename . '.phtml';
                break;
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Output::errorHandler
     *
     * Kernel defined error handler function.
     *
     * @param int    $errorSeverity The first parameter, errno, contains the level of the error raised, as an integer.
     * @param string $errorMessage  The second parameter, errstr, contains the error message, as a string.
     * @param string $errorFile     The third parameter is optional, errfile, which contains the filename that the error
     *                              was raised in, as a string.
     * @param string $errorLine     The fourth parameter is optional, errline, which contains the line number the error
     *                              was raised at, as an integer.
     * @param array  $errorContext  The fifth parameter is optional, errcontext, which is an array that points to the
     *                              active symbol table at the point the error occurred. In other words, errcontext will
     *                              contain an array of every variable that existed in the scope the error was triggered
     *                              in. User error handler must not modify error context.
     *
     * @return bool If the function returns FALSE then the normal error handler continues.
     * @throws ErrorException
     */
    public function errorHandler($errorSeverity, $errorMessage, $errorFile, $errorLine, $errorContext = [])
    {
        $isFatalError = (((E_ERROR | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR) & $errorSeverity) === $errorSeverity);

        if (strpos($errorFile, 'parser') !== false) {
            if (function_exists('parser')) {
                if (services()->has('presenter')) {
                    $vars = presenter()->getArrayCopy();
                    extract($vars);
                }

                $errorFile = str_replace(PATH_ROOT, DIRECTORY_SEPARATOR, parser()->getSourceFilePath());
                $error = new ErrorException($errorMessage, $errorSeverity, $errorFile, $errorLine, $errorContext);

                $filePath = $this->getFilePath('error');

                ob_start();
                include $filePath;
                $htmlOutput = ob_get_contents();
                ob_end_clean();

                echo $htmlOutput;

                return true;
            }
        }

        // When the error is fatal the Kernel will throw it as an exception.
        if ($isFatalError) {
            throw new ErrorException($errorMessage, $errorSeverity, $errorLine, $errorLine, $errorContext);
        }

        // Should we ignore the error? We'll get the current error_reporting
        // level and add its bits with the severity bits to find out.
        if (($errorSeverity & error_reporting()) !== $errorSeverity) {
            return false;
        }

        $error = new ErrorException($errorMessage, $errorSeverity, $errorFile, $errorLine, $errorContext);

        // Logged the error
        if(services()->has('logger')) {
            logger()->error(
                implode(
                    ' ',
                    [
                        '[ ' . $error->getStringSeverity() . ' ] ',
                        $error->getMessage(),
                        $error->getFile() . ':' . $error->getLine(),
                    ]
                )
            );
        }

        // Should we display the error?
        if (str_ireplace(['off', 'none', 'no', 'false', 'null'], 0, ini_get('display_errors')) == 1) {
            if (is_ajax()) {
                $this->setContentType('application/json');
                $this->statusCode = 500;
                $this->reasonPhrase = 'Internal Server Error';

                $this->send(implode(
                    ' ',
                    [
                        '[ ' . $error->getStringSeverity() . ' ] ',
                        $error->getMessage(),
                        $error->getFile() . ':' . $error->getLine(),
                    ]
                ));
                exit(EXIT_ERROR);
            }

            if (services()->has('presenter')) {
                if (presenter()->theme) {
                    presenter()->theme->load();
                }

                $vars = presenter()->getArrayCopy();
                extract($vars);
            }

            $filePath = $this->getFilePath('error');

            ob_start();
            include $filePath;
            $htmlOutput = ob_get_contents();
            ob_end_clean();

            if (services()->has('presenter')) {
                $htmlOutput = presenter()->assets->parseSourceCode($htmlOutput);
            }

            echo $htmlOutput;
            exit(EXIT_ERROR);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Output::sendError
     *
     * @param int               $code
     * @param null|array|string $vars
     * @param array             $headers
     */
    public function sendError($code = 204, $vars = null, $headers = [])
    {
        $languageKey = $code . '_' . error_code_string($code);

        $error = [
            'code'    => $code,
            'title'   => language()->getLine($languageKey . '_TITLE'),
            'message' => language()->getLine($languageKey . '_MESSAGE'),
        ];

        $this->statusCode = $code;
        $this->reasonPhrase = $error[ 'title' ];

        if (is_string($vars)) {
            $vars = ['message' => $vars];
        } elseif (is_array($vars) and empty($vars[ 'message' ])) {
            $vars[ 'message' ] = $error[ 'message' ];
        }

        if (isset($vars[ 'message' ])) {
            $error[ 'message' ] = $vars[ 'message' ];
        }

        if (is_ajax() or $this->mimeType !== 'text/html') {
            $this->statusCode = $code;
            $this->reasonPhrase = $error[ 'title' ];
            $this->send($vars);

            exit(EXIT_ERROR);
        }

        $this->sendHeaders($headers);

        if (services()->has('presenter')) {
            presenter()->initialize();

            if (presenter()->theme) {
                presenter()->theme->load();
            }

            $vars = presenter()->getArrayCopy();
            extract($vars);
        }

        extract($error);

        ob_start();
        include $this->getFilePath('error-code');
        $htmlOutput = ob_get_contents();
        ob_end_clean();

        if (services()->has('presenter')) {
            $htmlOutput = presenter()->assets->parseSourceCode($htmlOutput);
        }

        echo $htmlOutput;
        exit(EXIT_ERROR);
    }
}