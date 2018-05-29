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
    public $imagePath;
    public $imageNotFound = 'not-found.jpg';

    public $imageFilePath = null;
    public $imageFileMime = null;
    public $imageSize = [
        'width'  => null,
        'height' => null,
    ];

    public $imageScale = null;
    public $imageQuality = null;
    public $imageCrop = false;

    public function __construct()
    {
        $this->imagePath = PATH_STORAGE . 'images' . DIRECTORY_SEPARATOR;
        $this->imageNotFound = $this->imagePath . 'not-found.jpg';
    }

    public function route()
    {
        if (func_get_arg(0) === 'index') {
            $segments = func_get_arg(1);
        } else {
            $segments = array_merge([func_get_arg(0)], func_get_arg(1));
        }

        $this->imageFilePath = $this->imageNotFound;

        $this->imageSize[ 'width' ] = input()->get('width');
        $this->imageSize[ 'height' ] = input()->get('height');
        $this->imageScale = input()->get('scale');
        $this->imageQuality = input()->get('quality');
        $this->imageCrop = input()->get('crop');

        if (false !== ($key = array_search('crop', $segments))) {
            $this->imageCrop = true;
            unset($segments[ $key ]);
            $segments = array_values($segments);
        }

        if (count($segments) == 1) {
            $this->imageFilePath = $this->imagePath . end($segments);
        } elseif (count($segments) >= 2) {
            if (preg_match("/(\d+)(x)(\d+)/", $segments[ count($segments) - 2 ], $matches)) {
                $this->imageSize[ 'width' ] = $matches[ 1 ];
                $this->imageSize[ 'height' ] = $matches[ 3 ];

                if (count($segments) == 2) {
                    $this->imageFilePath = $this->imagePath . end($segments);
                } else {
                    $this->imageFilePath = $this->imagePath . implode(DIRECTORY_SEPARATOR,
                            array_slice($segments, 0,
                                count($segments) - 2)) . DIRECTORY_SEPARATOR . end($segments);
                }
            } elseif (preg_match("/(\d+)(p)/", $segments[ count($segments) - 2 ],
                    $matches) or is_numeric($segments[ count($segments) - 2 ])
            ) {
                $this->imageScale = isset($matches[ 1 ]) ? $matches[ 1 ] : $segments[ count($segments) - 2 ];
                if (count($segments) == 2) {
                    $this->imageFilePath = $this->imagePath . end($segments);
                } else {
                    $this->imageFilePath = $this->imagePath . implode(DIRECTORY_SEPARATOR,
                            array_slice($segments, 0,
                                count($segments) - 2)) . DIRECTORY_SEPARATOR . end($segments);
                }
            } else {
                $this->imageFilePath = $this->imagePath . implode(DIRECTORY_SEPARATOR, $segments);
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
            $this->imageFilePath = $this->imageNotFound;
        }

        if ( ! empty($this->imageScale)) {
            $this->scale();
        } elseif ( ! empty($this->imageSize[ 'width' ]) || ! empty($this->imageSize[ 'height' ])) {
            $this->resize();
        } else {
            $this->original();
        }
    }

    protected function scale()
    {
        $config = config('image', true);

        if ( ! empty($this->imageQuality)) {
            $config->offsetSet('quality', intval($this->imageQuality));
        }

        if ($config->cached === true) {
            if ($this->imageFilePath !== $this->imageNotFound) {
                $cacheImageKey = 'image-' . $this->imageScale . '-' . str_replace($this->imagePath, '',
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

    protected function resize()
    {
        $config = config('image', true);

        if ( ! empty($this->imageQuality)) {
            $config->offsetSet('quality', intval($this->imageQuality));
        }

        if ($config->cached === true) {
            if ($this->imageFilePath !== $this->imageNotFound) {
                $cacheImageKey = 'image-' . (input()->get('crop') ? 'crop-' : '') . implode('x',
                        $this->imageSize) . '-' . str_replace($this->imagePath, '', $this->imageFilePath);

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