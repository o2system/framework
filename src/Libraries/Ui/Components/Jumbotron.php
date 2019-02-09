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

namespace O2System\Framework\Libraries\Ui\Components;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components\Jumbotron\Paragraph;
use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Jumbotron
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class Jumbotron extends Element
{
    /**
     * Jumbotron::__construct
     */
    public function __construct()
    {
        parent::__construct('div');
        $this->attributes->addAttributeClass('jumbotron');
    }

    // ------------------------------------------------------------------------

    /**
     * Jumbotron::setImageBackground
     *
     * @param string $src
     *
     * @return static
     */
    public function setImageBackground($src)
    {
        $this->attributes->addAttributeClass('jumbotron-bg');

        if (is_file($src)) {
            $src = path_to_url($src);
        }

        $this->attributes->addAttribute('style', 'background-image: url(\'' . $src . '\');');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Jumbotron::setVideoBackground
     *
     * @param string        $src
     * @param string|null   $poster
     *
     * @return static
     */
    public function setVideoBackground($src, $poster = null)
    {
        $this->attributes->addAttributeClass('jumbotron-video');

        $video = new Element('video', 'jumbotron-video');

        if (isset($poster)) {
            $video->attributes->addAttribute('poster', $poster);
        }

        $video->attributes->addAttribute('width', '100%');
        $video->attributes->addAttribute('preload', 'auto');
        $video->attributes->addAttribute('loop', null);
        $video->attributes->addAttribute('autoplay', null);
        $video->attributes->addAttribute('muted', null);

        $source = new Element('source', 'jumbotron-video-source');

        if (is_file($src)) {
            $src = path_to_url($src);
        }

        $source->attributes->addAttribute('src', $src);
        $source->attributes->addAttribute('type', 'video/webm');

        $video->textContent->push($source->render());

        $this->childNodes->prepend($video);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Jumbotron::setCarousel
     *
     * @param \O2System\Framework\Libraries\Ui\Components\Carousel $carousel
     *
     * @return static
     */
    public function setCarousel(Carousel $carousel)
    {
        $this->attributes->addAttributeClass('jumbotron-carousel');
        $this->childNodes->prepend($carousel);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Jumbotron::createHeader
     *
     * @param string $text
     * @param string $tagName
     * @param array  $attributes
     *
     * @return Element
     */
    public function createHeader($text, $tagName = 'h1', array $attributes = ['class' => 'display-3'])
    {
        $header = new Element($tagName, 'header-' . dash($text));
        $header->textContent->push($text);

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $header->attributes->addAttribute($name, $value);
            }
        }

        $this->childNodes->push($header);

        return $this->childNodes->last();
    }

    // ------------------------------------------------------------------------

    /**
     * Jumbotron::createHorizontalRule
     *
     * @param array $attributes
     *
     * @return Element
     */
    public function createHorizontalRule(array $attributes = [])
    {
        $hr = new Element('hr');

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $hr->attributes->addAttribute($name, $value);
            }
        }

        $this->childNodes->push($hr);

        return $this->childNodes->last();
    }

    // ------------------------------------------------------------------------

    /**
     * Jumbotron::createParagraph
     *
     * @param string|null  $text
     * @param array        $attributes
     *
     * @return Paragraph
     */
    public function createParagraph($text = null, array $attributes = [])
    {
        $paragraph = new Paragraph();

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $paragraph->attributes->addAttribute($name, $value);
            }
        }

        if ($text instanceof Element) {
            $paragraph->childNodes->push($text);
        } elseif ( ! is_null($text)) {
            $paragraph->textContent->push($text);
        }

        $this->childNodes->push($paragraph);

        return $this->childNodes->last();
    }

    // ------------------------------------------------------------------------

    /**
     * Jumbotron::fluid
     *
     * @return static
     */
    public function fluid()
    {
        $this->attributes->addAttributeClass('jumbotron-fluid');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Jumbotron::render
     *
     * @return string
     */
    public function render()
    {
        if ($this->attributes->hasAttributeClass('jumbotron-fluid')) {

            $output[] = $this->open();

            $container = new Element('div', 'container');
            $container->attributes->addAttributeClass('container-fluid');

            if ($this->hasChildNodes()) {
                foreach ($this->childNodes as $childNode) {
                    $container->childNodes->push($childNode);
                }
            }

            $output[] = $container;
            $output[] = $this->close();

            return implode(PHP_EOL, $output);
        }

        return parent::render();
    }
}