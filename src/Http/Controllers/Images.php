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
    public $imagesPath;
    public $imagesNotFound = 'not-found.jpg';

    public function __construct()
    {
        $this->imagesPath = PATH_STORAGE . 'images' . DIRECTORY_SEPARATOR;
        $this->imagesNotFound = $this->imagesPath . 'not-found.jpg';
    }

    public function route()
    {
        $segments = array_merge([func_get_arg(0)], func_get_arg(1));
        $filePath = $this->imagesNotFound;

        if (false !== ($key = array_search('crop', $segments))) {
            $_GET['crop'] = true;
            unset($segments[$key]);
            $segments = array_values($segments);
        }

        if (count($segments) == 1) {
            $filePath = $this->imagesPath . end($segments);
        } elseif (count($segments) >= 2) {
            if (preg_match("/(\d+)(x)(\d+)/", $segments[count($segments) - 2], $matches)) {
                $size['width'] = $matches[1];
                $size['height'] = $matches[3];

                if (count($segments) == 2) {
                    $filePath = $this->imagesPath . end($segments);
                } else {
                    $filePath = $this->imagesPath . implode(DIRECTORY_SEPARATOR,
                            array_slice($segments, 0,
                                count($segments) - 2)) . DIRECTORY_SEPARATOR . end($segments);
                }
            } elseif (preg_match("/(\d+)(p)/", $segments[count($segments) - 2],
                    $matches) or is_numeric($segments[count($segments) - 2])
            ) {
                $scale = isset($matches[1]) ? $matches[1] : $segments[count($segments) - 2];
                if (count($segments) == 2) {
                    $filePath = $this->imagesPath . end($segments);
                } else {
                    $filePath = $this->imagesPath . implode(DIRECTORY_SEPARATOR,
                            array_slice($segments, 0,
                                count($segments) - 2)) . DIRECTORY_SEPARATOR . end($segments);
                }
            } else {
                $filePath = $this->imagesPath . implode(DIRECTORY_SEPARATOR, $segments);
            }
        }

        if (!is_file($filePath)) {
            $filePath = $this->imagesNotFound;
        }

        if (isset($scale)) {
            $this->scale($filePath, $scale);
        } elseif (isset($size)) {
            $this->resize($filePath, $size);
        } else {
            $this->original($filePath);
        }
    }

    protected function original($filePath)
    {
        $manipulation = new Manipulation(config('image', true));
        $manipulation->setImageFile($filePath);
        $manipulation->displayImage();
        exit(EXIT_SUCCESS);
    }

    protected function resize($filePath, $size)
    {
        $config = config('image', true);

        if (input()->get('crop')) {
            $config->offsetSet('autoCrop', true);
        }

        if ($config->cached === true) {
            if ($filePath !== $this->imagesNotFound) {
                $cacheImageKey = 'image-' . (input()->get('crop') ? 'crop-' : '') . implode('x',
                        $size) . '-' . str_replace($this->imagesPath, '', $filePath);

                if (cache()->hasItemPool('images')) {
                    $cacheItemPool = cache()->getItemPool('images');

                    if ($cacheItemPool->hasItem($cacheImageKey)) {
                        $cacheImageString = $cacheItemPool->getItem($cacheImageKey)->get();

                        $manipulation = new Manipulation($config);
                        $manipulation->setImageFile($filePath);
                        $manipulation->setImageString($cacheImageString);
                        $manipulation->displayImage();
                    } else {
                        $manipulation = new Manipulation($config);
                        $manipulation->setImageFile($filePath);
                        $manipulation->resizeImage($size['width'], $size['height']);
                        $cacheItemPool->save(new Item($cacheImageKey, $manipulation->getBlobImage(), false));

                        $manipulation->displayImage();
                        exit(EXIT_SUCCESS);
                    }
                }
            }
        }


        $manipulation = new Manipulation($config);
        $manipulation->setImageFile($filePath);
        $manipulation->resizeImage($size['width'], $size['height']);
        $manipulation->displayImage();
        exit(EXIT_SUCCESS);
    }

    protected function scale($filePath, $scale)
    {
        if (config('image', true)->cached === true) {
            if ($filePath !== $this->imagesNotFound) {
                $cacheImageKey = 'image-' . $scale . '-' . str_replace($this->imagesPath, '', $filePath);

                if (cache()->hasItemPool('images')) {
                    $cacheItemPool = cache()->getItemPool('images');

                    if ($cacheItemPool->hasItem($cacheImageKey)) {
                        $cacheImageString = $cacheItemPool->getItem($cacheImageKey)->get();

                        $manipulation = new Manipulation(config('image', true));
                        $manipulation->setImageFile($filePath);
                        $manipulation->setImageString($cacheImageString);
                        $manipulation->displayImage();
                    } else {
                        $manipulation = new Manipulation(config('image', true));
                        $manipulation->setImageFile($filePath);
                        $manipulation->scaleImage($scale);
                        $cacheItemPool->save(new Item($cacheImageKey, $manipulation->getBlobImage(), false));

                        $manipulation->displayImage();
                        exit(EXIT_SUCCESS);
                    }
                }
            }
        }

        $manipulation = new Manipulation(config('image', true));
        $manipulation->setImageFile($filePath);
        $manipulation->scaleImage($scale);
        $manipulation->displayImage();
        exit(EXIT_SUCCESS);
    }
}