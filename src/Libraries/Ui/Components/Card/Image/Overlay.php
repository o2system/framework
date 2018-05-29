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

namespace O2System\Framework\Libraries\Ui\Components\Card\Image;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;
use O2System\Framework\Libraries\Ui\Traits\Collectors\ParagraphsCollectorTrait;
use O2System\Framework\Libraries\Ui\Traits\Setters\TitleSetterTrait;
use O2System\Spl\Iterators\ArrayIterator;

/**
 * Class Overlay
 *
 * @package O2System\Framework\Libraries\Ui\Components\Card\Image
 */
class Overlay extends Element
{
    use TitleSetterTrait;
    use ParagraphsCollectorTrait;

    public function __construct()
    {
        parent::__construct('div', 'overlay');
        $this->attributes->addAttributeClass('card-img-overlay');
    }

    public function render()
    {
        $output[] = $this->open();

        if ($this->title instanceof Element) {
            $this->title->attributes->addAttributeClass('card-title');
            $output[] = $this->title;
        }

        if ($this->subTitle instanceof Element) {
            $this->subTitle->attributes->addAttributeClass('card-subtitle');
            $output[] = $this->subTitle;
        }

        if ($this->paragraphs instanceof ArrayIterator) {
            if ($this->paragraphs->count()) {
                foreach ($this->paragraphs as $paragraph) {
                    $paragraph->attributes->addAttributeClass('card-text');
                    $output[] = $paragraph;
                }
            }
        }

        if ($this->hasTextContent()) {
            $output[] = implode('', $this->textContent);
        }

        if ($this->hasChildNodes()) {
            $output[] = implode(PHP_EOL, $this->childNodes);
        }

        $output[] = $this->close();

        return implode(PHP_EOL, $output);
    }
}