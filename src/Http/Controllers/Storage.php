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

namespace O2System\Framework\Http\Controllers;

// ------------------------------------------------------------------------

use O2System\Filesystem\Handlers\Downloader;
use O2System\Framework\Http\Controller;
use O2System\Spl\Info\SplFileInfo;

/**
 * Class Storage
 *
 * @package O2System\Framework\Http\Controllers
 */
class Storage extends Controller
{
    /**
     * Storage::$inherited
     *
     * Controller inherited flag.
     *
     * @var bool
     */
    static public $inherited = true;

    /**
     * Storage::$directoryPath
     *
     * @var string
     */
    public $directoryPath = PATH_STORAGE;

    /**
     * Storage::$speedLimit
     *
     * @var int
     */
    public $speedLimit = 1024;

    /**
     * Storage::$resumeable
     *
     * @var bool
     */
    public $resumeable = true;

    // ------------------------------------------------------------------------

    /**
     * Storage::route
     */
    public function route()
    {
        $segments = server_request()->getUri()->segments->getArrayCopy();
        array_shift($segments);

        $download = false;

        if (false !== ($key = array_search('download', $segments))) {
            $download = true;
            unset($segments[ $key ]);
            $segments = array_values($segments);
        }

        if (count($segments)) {
            $filePath = $this->directoryPath . implode(DIRECTORY_SEPARATOR, $segments);

            if (is_file($filePath)) {
                if ($download) {
                    $downloader = new Downloader($filePath);
                    $downloader
                        ->speedLimit($this->speedLimit)
                        ->resumeable($this->resumeable)
                        ->download();
                } elseif ( ! $fileHandle = @fopen($filePath, 'rb')) {
                    redirect_url('error/505');
                } else {
                    $fileInfo = new SplFileInfo($filePath);

                    if(in_array($fileInfo->getExtension(), ['json', 'txt', 'xml', 'js', 'css'])) {
                        header('Content-Disposition: filename=' . $fileInfo->getFilename());
                        header('Content-Transfer-Encoding: binary');
                        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
                        header('Content-Type: ' . $fileInfo->getMime());
                        echo file_get_contents($filePath);
                        exit;
                    } else {
                        $fileChunkSize = 1024*1024;
                        $lengthStart = 0;
                        $lengthEnd = $fileInfo->getSize();

                        if ($httpRange = input()->server('HTTP_RANGE')) {
                            if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $httpRange, $matches)) {
                                $lengthStart = intval($matches[ 0 ]);
                                if ( ! empty($matches[ 1 ])) {
                                    $lengthEnd = intval($matches[ 1 ]);
                                }
                            }
                        }

                        if ($lengthStart > 0 || $lengthEnd < $fileInfo->getSize()) {
                            header('HTTP/1.0 206 Partial Content');
                        } else {
                            header('HTTP/1.0 200 OK');
                        }

                        header('Content-Type: ' . $fileInfo->getMime());
                        header('Cache-Control: max-age=60');
                        header('Content-Length:' . ($lengthEnd - $lengthStart));
                        header("Content-Range: bytes " . ($lengthStart-$lengthEnd)/$fileInfo->getSize());
                        header("Content-Disposition: inline; filename=" . $fileInfo->getFilename() . '.' . $fileInfo->getExtension());
                        header("Content-Transfer-Encoding: binary\n");
                        header("Last-Modified: " . gmdate('D, d M Y H:i:s', $fileInfo->getMTime()) . ' GMT');
                        header('Connection: close');

                        $lengthCurrent = $lengthStart;
                        fseek($fileHandle, $lengthStart, 0);

                        $buffer = '';
                        ob_start();
                        while ( ! feof($fileHandle) && $lengthCurrent < $lengthEnd && (connection_status() == 0)) {
                            echo fread($fileHandle, min($fileChunkSize, $lengthEnd - $lengthCurrent));
                            $lengthCurrent += $fileChunkSize;
                            $buffer.= ob_get_contents();

                            ob_end_flush();
                        }

                        echo $buffer;
                        exit;
                    }
                }
            } else {
                redirect_url('error/404');
            }
        } else {
            redirect_url('error/403');
        }
    }
}