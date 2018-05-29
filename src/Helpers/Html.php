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
/**
 * HTML Helper
 *
 * A collection of helper function to work with html.
 */
// ------------------------------------------------------------------------

if ( ! function_exists('tag')) {
    /**
     * tag
     *
     * Generate html tag with auto closed when the content is set.
     *
     * @param string      $tagName
     * @param string|null $contents
     * @param array       $attributes
     *
     * return string
     */
    function tag($tagName, $contents = null, array $attributes = [])
    {
        if (($tag = substr($tagName, 0)) === '/') {
            $element = new \O2System\Html\Element($tag);

            return $element->close();
        } else {
            $element = new \O2System\Html\Element($tagName);
            if (count($attributes)) {
                foreach ($attributes as $name => $value) {
                    $element->attributes->addAttribute($name, $value);
                }
            }

            if (is_array($contents)) {
                foreach ($contents as $content) {
                    if ($content instanceof \O2System\Html\Element) {
                        $element->childNodes->push($content);
                    } elseif (is_string($content) || is_numeric($content)) {
                        $element->textContent->push($content);
                    }
                }
            } elseif ($contents instanceof \O2System\Html\Element) {
                $element->childNodes->push($contents);
            } elseif (is_string($contents) || is_numeric($contents)) {
                $element->textContent->push($contents);
            }

            if ( ! is_null($contents)) {
                return $element->render();
            } else {
                return $element->open();
            }
        }
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('video')) {
    /**
     * video
     *
     * Generate video, such as a movie clip or other video streams.
     *
     * @param string $src
     * @param array  $attributes
     *
     * @return string
     */
    function video($src, array $attributes = [])
    {
        $attributes = array_merge([
            'width'    => 320,
            'height'   => 240,
            'controls' => 'controls',
        ], $attributes);

        $video = new \O2System\Html\Element('video');

        foreach ($attributes as $name => $value) {
            $video->attributes->addAttribute($name, $value);
        }

        $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));

        if (in_array($ext, ['mp4', 'ogg', 'webm'])) {
            $source = new \O2System\Html\Element('source');

            if (is_file($src)) {
                $src = path_to_url($src);
            }

            $source->attributes->addAttribute('src', $src);
            $source->attributes->addAttribute('type', 'video/' . $ext);
            $video->childNodes->push($source);
        }

        $video->textContent->push(language()->getLine('VIDEO_NOT_SUPPORTED'));

        return $video->render();
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('audio')) {
    /**
     * audio
     *
     * Generate audio, such as music or other audio streams.
     *
     * @param string $src
     * @param array  $attributes
     *
     * @return string
     */
    function audio($src, array $attributes = [])
    {
        $attributes = array_merge([
            'controls' => 'controls',
        ], $attributes);

        $audio = new \O2System\Html\Element('audio');

        foreach ($attributes as $name => $value) {
            $audio->attributes->addAttribute($name, $value);
        }

        $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));

        if (in_array($ext, ['mpeg', 'ogg', 'wav'])) {
            $source = new \O2System\Html\Element('source');

            if (is_file($src)) {
                $src = path_to_url($src);
            }

            $source->attributes->addAttribute('src', $src);
            $source->attributes->addAttribute('type', 'audio/' . $ext);
            $audio->childNodes->push($source);
        }

        $audio->textContent->push(language()->getLine('AUDIO_NOT_SUPPORTED'));

        return $audio->render();
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('canvas')) {
    /**
     * canvas
     *
     * Generate canvas html element.
     *
     * @param array $attributes
     *
     * @return string
     */
    function canvas(array $attributes = [])
    {
        $canvas = new \O2System\Html\Element('canvas');

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $canvas->attributes->addAttribute($name, $value);
            }
        }

        $canvas->textContent->push(language()->getLine('CANVAS_NOT_SUPPORTED'));

        return $canvas->render();
    }
}
// ------------------------------------------------------------------------

if ( ! function_exists('heading')) {
    /**
     * heading
     *
     * Generates html heading tag.
     *
     * @param string $textContent
     * @param int    $level
     * @param array  $attributes
     *
     * @return string
     */
    function heading($textContent = '', $level = 1, array $attributes = [])
    {
        return (new \O2System\Framework\Libraries\Ui\Contents\Heading($textContent, $level, $attributes))->render();
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('ul')) {
    /**
     * ul
     *
     * Generates an HTML unordered list from an single or multi-dimensional array.
     *
     * @param    array $list
     * @param    array $attributes
     *
     * @return    string
     */
    function ul($list, array $attributes = [])
    {
        return (new \O2System\Framework\Libraries\Ui\Contents\Lists\Unordered($attributes))->createLists($list)->render();
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('ol')) {
    /**
     * ol
     *
     * Generates an HTML ordered list from an single or multi-dimensional array.
     *
     * @param    array $list
     * @param    array $attributes
     *
     * @return    string
     */
    function ol($list, array $attributes = [])
    {
        return (new \O2System\Framework\Libraries\Ui\Contents\Lists\Ordered($attributes))->createLists($list)->render();
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('img')) {
    /**
     * img
     *
     * @param string $src
     * @param string $attributes
     *
     * @return string
     */
    function img($src = '', $alt, array $attributes = [])
    {
        $img = new \O2System\Framework\Libraries\Ui\Contents\Image($src, $alt);

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $img->attributes->addAttribute($name, $value);
            }
        }
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('meta')) {
    /**
     * meta
     *
     * Generates meta tags from an array of key/values
     *
     * @param    $meta      string|array
     * @param    $content   string|null
     * @param    $type      string
     *
     * @return    string
     */
    function meta($meta = '', $content = '', $type = 'name')
    {
        // Since we allow the data to be passes as a string, a simple array
        // or a multidimensional one, we need to do a little prepping.
        if ( ! is_array($meta)) {
            $meta = [['name' => $meta, 'content' => $content, 'type']];
        } elseif (isset($meta[ 'name' ])) {
            // Turn single array into multidimensional
            $meta = [$meta];
        }

        $output = [];

        foreach ($meta as $attributes) {
            $element = new \O2System\Html\Element('meta');
            $element->attributes->addAttribute('type',
                (isset($attributes[ 'type' ]) && $attributes[ 'type' ] !== 'name') ? 'http-equiv' : 'name');
            $element->attributes->addAttribute('name',
                isset($attributes[ 'content' ]) ? $attributes[ 'content' ] : '');
            $element->attributes->addAttribute('name',
                isset($attributes[ 'content' ]) ? $attributes[ 'content' ] : '');

            if (count($attributes)) {
                foreach ($attributes as $meta => $value) {
                    $element->attributes->addAttribute($meta, $value);
                }
            }

            $output[] = $element;
        }

        return implode(PHP_EOL, $output);
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('parse_attributes')) {
    /**
     * parse_attributes
     *
     * Parse attributes from html tag string.
     *
     * @param $string
     *
     * @return array
     */
    function parse_attributes($string)
    {
        $attributes = [];

        if (is_string($string)) {
            if (is_html($string)) {
                $xml = simplexml_load_string(str_replace('>', '/>', $string));
            } else {
                $xml = simplexml_load_string('<tag ' . $string . '/>');
            }

            foreach ($xml->attributes() as $key => $node) {
                $attributes[ $key ] = (string)$node;
            }
        }

        return $attributes;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('remove_tags')) {
    /**
     * Remove Tag
     *
     * Remove the tags but keep the content.
     * Note this function always assumed no two tags start the same way (e.g. <tag> and <tags>)
     *
     * @param   string       $html          HTML Source Code
     * @param   string|array $tags          Single HTML Tag | List of HTML Tag
     * @param   bool         $strip_content Whether to display the content of inside tag or erase it
     *
     * @return  string
     */
    function remove_tags($html, $tags, $strip_content = false)
    {
        $content = '';
        if ( ! is_array($tags)) {
            $tags = (strpos($html, '>') !== false ? explode('>', str_replace('<', '', $tags)) : [$tags]);
            if (end($tags) == '') {
                array_pop($tags);
            }
        }
        foreach ($tags as $tag) {
            if ($strip_content) {
                $content = '(.+</' . $tag . '[^>]*>|)';
            }

            $html = preg_replace('#</?' . $tag . '[^>]*>' . $content . '#is', '', $html);
        }

        return $html;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('extract_tag')) {
    /**
     * Extract Tag
     *
     * Extract content inside tag.
     *
     * @param  string  $html HTML Source Code
     * @param   string $tag  HTML Tag
     *
     * @return  string
     */
    function extract_tag($html, $tag = 'div')
    {
        $html = preg_match_all("/(\<" . $tag . ")(.*?)(" . $tag . ">)/si", $html, $matches);

        $result = '';
        foreach ($matches[ 0 ] as $item) {
            $result = preg_replace("/\<[\/]?" . $tag . "\>/", '', $item);
        }

        return $result;
    }
}