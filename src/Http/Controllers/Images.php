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

use O2System\Cache\Item;
use O2System\Framework\Http\Controller;
use O2System\Image\Manipulation;

/**
 * Class Images
 *
 * @package O2System\Framework\Http\Controllers
 */
class Images extends Controller
{
    /**
     * Images::$inherited
     *
     * Controller inherited flag.
     *
     * @var bool
     */
    static public $inherited = true;

    /**
     * Images::$storagePath
     *
     * @var string
     */
    public $storagePath;

    /**
     * Images::$imageNotFoundFilename
     *
     * @var string
     */
    public $imageNotFoundFilename = 'not-found.jpg';

    /**
     * Images::$imageFilePath
     *
     * @var string
     */
    public $imageFilePath = null;

    /**
     * Images::$imageFileMime
     *
     * @var string
     */
    public $imageFileMime = null;

    /**
     * Images::$imageSize
     *
     * @var array
     */
    public $imageSize = [
        'width'  => null,
        'height' => null,
    ];

    /**
     * Images::$imageScale
     *
     * @var string
     */
    public $imageScale = null;

    /**
     * Images::$imageQuality
     *
     * @var int
     */
    public $imageQuality = null;

    /**
     * Images::$imageCrop
     *
     * @var bool
     */
    public $imageCrop = false;

    // ------------------------------------------------------------------------

    /**
     * Images::__construct
     */
    public function __construct()
    {
        $this->storagePath = PATH_STORAGE . 'images' . DIRECTORY_SEPARATOR;
        $this->imageNotFoundFilename = $this->storagePath . 'not-found.jpg';
    }

    // ------------------------------------------------------------------------

    /**
     * Images::route
     */
    public function route()
    {
        $segments = server_request()->getUri()->segments->getArrayCopy();

        if (false !== ($key = array_search('images', $segments))) {
            $segments = array_slice($segments, $key);
        } else {
            array_shift($segments);
        }

        $this->imageFilePath = $this->imageNotFoundFilename;

        $this->imageSize[ 'width' ] = $this->input->get('width');
        $this->imageSize[ 'height' ] = $this->input->get('height');
        $this->imageScale = $this->input->get('scale');
        $this->imageQuality = $this->input->get('quality');
        $this->imageCrop = $this->input->get('crop');

        if (false !== ($key = array_search('crop', $segments))) {
            $this->imageCrop = true;
            unset($segments[ $key ]);
            $segments = array_values($segments);
        }

        if (count($segments) == 1) {
            $this->imageFilePath = $this->storagePath . end($segments);
        } elseif (count($segments) >= 2) {
            if (preg_match("/(\d+)(x)(\d+)/", $segments[ count($segments) - 2 ], $matches)) {
                $this->imageSize[ 'width' ] = $matches[ 1 ];
                $this->imageSize[ 'height' ] = $matches[ 3 ];

                if (count($segments) == 2) {
                    $this->imageFilePath = $this->storagePath . end($segments);
                } else {
                    $this->imageFilePath = $this->storagePath . implode(DIRECTORY_SEPARATOR,
                            array_slice($segments, 0,
                                count($segments) - 2)) . DIRECTORY_SEPARATOR . end($segments);
                }
            } elseif (preg_match("/(\d+)(p)/", $segments[ count($segments) - 2 ],
                    $matches) or is_numeric($segments[ count($segments) - 2 ])
            ) {
                $this->imageScale = isset($matches[ 1 ]) ? $matches[ 1 ] : $segments[ count($segments) - 2 ];
                if (count($segments) == 2) {
                    $this->imageFilePath = $this->storagePath . end($segments);
                } else {
                    $this->imageFilePath = $this->storagePath . implode(DIRECTORY_SEPARATOR,
                            array_slice($segments, 0,
                                count($segments) - 2)) . DIRECTORY_SEPARATOR . end($segments);
                }
            } else {
                $this->imageFilePath = $this->storagePath . implode(DIRECTORY_SEPARATOR, $segments);
            }
        }

        $imageFilePath = $this->imageFilePath;
        $extensions[ 0 ] = pathinfo($imageFilePath, PATHINFO_EXTENSION);

        for ($i = 0; $i < 2; $i++) {
            $extension = pathinfo($imageFilePath, PATHINFO_EXTENSION);

            if ($extension !== '') {
                $extensions[ $i ] = $extension;
                $imageFilePath = str_replace('.' . $extensions[ $i ], '', $imageFilePath);
            }
        }

        $mimes = [
            'gif'  => 'image/gif',
            'jpg'  => 'image/jpeg',
            'png'  => 'image/png',
            'webp' => 'image/webp',
        ];

        if (count($extensions) == 2) {
            $this->imageFilePath = $imageFilePath . '.' . $extensions[ 1 ];
        }

        if (array_key_exists($extension = reset($extensions), $mimes)) {
            $this->imageFileMime = $mimes[ $extension ];
        } elseif (array_key_exists($extension = pathinfo($this->imageFilePath, PATHINFO_EXTENSION), $mimes)) {
            $this->imageFileMime = $mimes[ $extension ];
        }

        if ( ! is_file($this->imageFilePath)) {
            $this->imageFilePath = $this->imageNotFoundFilename;
        }

        if ( ! empty($this->imageScale)) {
            $this->scale();
        } elseif ( ! empty($this->imageSize[ 'width' ]) || ! empty($this->imageSize[ 'height' ])) {
            $this->resize();
        } else {
            $this->original();
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Images::scale
     *
     * @throws \O2System\Spl\Exceptions\Runtime\FileNotFoundException
     */
    protected function scale()
    {
        $config = config('image', true);

        if ( ! empty($this->imageQuality)) {
            $config->offsetSet('quality', intval($this->imageQuality));
        }

        if ($config->cached === true) {
            if ($this->imageFilePath !== $this->imageNotFoundFilename) {
                $cacheImageKey = 'image-' . $this->imageScale . '-' . str_replace($this->storagePath, '',
                        $this->imageFilePath);

                if (cache()->hasItemPool('images')) {
                    $cacheItemPool = cache()->getItemPool('images');

                    if ($cacheItemPool->hasItem($cacheImageKey)) {
                        $cacheImageString = $cacheItemPool->getItem($cacheImageKey)->get();

                        $manipulation = new Manipulation($config);
                        $manipulation->setImageFile($this->imageFilePath);
                        $manipulation->setImageString($cacheImageString);
                        $manipulation->displayImage(intval($this->imageQuality), $this->imageFileMime);
                    } else {
                        $manipulation = new Manipulation($config);
                        $manipulation->setImageFile($this->imageFilePath);
                        $manipulation->scaleImage($this->imageScale);
                        $cacheItemPool->save(new Item($cacheImageKey, $manipulation->getBlobImage(), false));

                        $manipulation->displayImage(intval($this->imageQuality), $this->imageFileMime);
                        exit(EXIT_SUCCESS);
                    }
                }
            }
        }

        $manipulation = new Manipulation($config);
        $manipulation->setImageFile($this->imageFilePath);
        $manipulation->scaleImage($this->imageScale);
        $manipulation->displayImage(intval($this->imageQuality), $this->imageFileMime);
        exit(EXIT_SUCCESS);
    }

    // ------------------------------------------------------------------------

    /**
     * Images::resize
     *
     * @throws \O2System\Spl\Exceptions\Runtime\FileNotFoundException
     */
    protected function resize()
    {
        $config = config('image', true);

        if ( ! empty($this->imageQuality)) {
            $config->offsetSet('quality', intval($this->imageQuality));
        }

        if ($config->cached === true) {
            if ($this->imageFilePath !== $this->imageNotFoundFilename) {
                $cacheImageKey = 'image-' . ($this->input->get('crop') ? 'crop-' : '') . implode('x',
                        $this->imageSize) . '-' . str_replace($this->storagePath, '', $this->imageFilePath);

                if (cache()->hasItemPool('images')) {
                    $cacheItemPool = cache()->getItemPool('images');

                    if ($cacheItemPool->hasItem($cacheImageKey)) {
                        $cacheImageString = $cacheItemPool->getItem($cacheImageKey)->get();

                        $manipulation = new Manipulation($config);
                        $manipulation->setImageFile($this->imageFilePath);
                        $manipulation->setImageString($cacheImageString);
                        $manipulation->displayImage(intval($this->imageQuality), $this->imageFileMime);
                    } else {
                        $manipulation = new Manipulation($config);
                        $manipulation->setImageFile($this->imageFilePath);
                        $manipulation->resizeImage($this->imageSize[ 'width' ], $this->imageSize[ 'height' ],
                            (bool)$this->imageCrop);
                        $cacheItemPool->save(new Item($cacheImageKey, $manipulation->getBlobImage(), false));

                        $manipulation->displayImage(intval($this->imageQuality), $this->imageFileMime);
                        exit(EXIT_SUCCESS);
                    }
                }
            }
        }

        $manipulation = new Manipulation($config);
        $manipulation->setImageFile($this->imageFilePath);
        $manipulation->resizeImage($this->imageSize[ 'width' ], $this->imageSize[ 'height' ], (bool)$this->imageCrop);
        $manipulation->displayImage(intval($this->imageQuality), $this->imageFileMime);
        exit(EXIT_SUCCESS);
    }

    // ------------------------------------------------------------------------

    /**
     * Images::original
     *
     * @throws \O2System\Spl\Exceptions\Runtime\FileNotFoundException
     */
    protected function original()
    {
        $config = config('image', true);

        if ( ! empty($this->imageQuality)) {
            $config->offsetSet('quality', intval($this->imageQuality));
        }

        $manipulation = new Manipulation($config);
        $manipulation->setImageFile($this->imageFilePath);
        $manipulation->displayImage(intval($this->imageQuality), $this->imageFileMime);
        exit(EXIT_SUCCESS);
    }
}