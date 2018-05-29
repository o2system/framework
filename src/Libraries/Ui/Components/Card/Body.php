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

namespace O2System\Framework\Libraries\Ui\Components\Card;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components\Card\Body\Author;
use O2System\Framework\Libraries\Ui\Components\Card\Body\Blockquote;
use O2System\Framework\Libraries\Ui\Components\Card\Body\Testimonial;
use O2System\Framework\Libraries\Ui\Element;
use O2System\Framework\Libraries\Ui\Grid\Row;
use O2System\Framework\Libraries\Ui\Traits\Collectors\LinksCollectorTrait;
use O2System\Framework\Libraries\Ui\Traits\Setters\ParagraphSetterTrait;
use O2System\Framework\Libraries\Ui\Traits\Setters\TitleSetterTrait;
use O2System\Spl\Iterators\ArrayIterator;

/**
 * Class Body
 *
 * @package O2System\Framework\Libraries\Ui\Components\Card
 */
class Body extends Element
{
    use TitleSetterTrait;
    use ParagraphSetterTrait;
    use LinksCollectorTrait;

    // ------------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct('div', 'card-body');
        $this->attributes->addAttributeClass('card-body');
    }

    // ------------------------------------------------------------------------

    public function createRow()
    {
        $row = new Row();

        $this->childNodes->push($row);

        return $this->childNodes->last();
    }

    // ------------------------------------------------------------------------

    /**
     * Body::createBlockquote
     *
     * @param string|null $text
     *
     * @return \O2System\Framework\Libraries\Ui\Components\Card\Body\Blockquote
     */
    public function createBlockquote($text = null)
    {
        $blockquote = new Blockquote();

        if (isset($text)) {
            $blockquote->setParagraph($text);
        }

        $this->childNodes->push($blockquote);

        return $this->childNodes->last();
    }

    // ------------------------------------------------------------------------

    /**
     * Body::createTestimonial
     *
     * @param string|null $text
     *
     * @return \O2System\Framework\Libraries\Ui\Components\Card\Body\Testimonial
     */
    public function createTestimonial($text = null)
    {
        $testimonial = new Testimonial();

        if (isset($text)) {
            $testimonial->setParagraph($text);
        }

        $this->childNodes->push($testimonial);

        return $this->childNodes->last();
    }

    // ------------------------------------------------------------------------

    /**
     * Body::createAuthor
     *
     * @return \O2System\Framework\Libraries\Ui\Components\Card\Body\Author
     */
    public function createAuthor()
    {
        $this->childNodes->push(new Author());

        return $this->childNodes->last();
    }

    public function createPrice($price, $discount = null, $validUntil = null)
    {

    }

    // ------------------------------------------------------------------------

    public function render()
    {
        if ($this->title instanceof Element) {
            $this->title->attributes->addAttributeClass('card-title');
            $this->childNodes->push($this->title);
        }

        if ($this->subTitle instanceof Element) {
            $this->subTitle->attributes->addAttributeClass('card-subtitle');
            $this->childNodes->push($this->subTitle);
        }

        if ($this->paragraph instanceof Element) {
            $this->paragraph->attributes->addAttributeClass('card-text');
            $this->childNodes->push($this->paragraph);
        }

        if ($this->links instanceof ArrayIterator) {
            if ($this->links->count()) {
                foreach ($this->links as $link) {
                    $link->attributes->addAttributeClass('card-link');
                    $this->childNodes->push($link);
                }
            }
        }

        if ($this->hasChildNodes()) {
            return parent::render();
        }

        return '';
    }
}