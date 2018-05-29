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

namespace O2System\Framework\Libraries\Ui\Components\Card\Body;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;
use O2System\Framework\Libraries\Ui\Traits\Setters\ParagraphSetterTrait;

/**
 * Class Testimonial
 *
 * @package O2System\Framework\Libraries\Ui\Components\Card\Body
 */
class Testimonial extends Element
{
    use ParagraphSetterTrait;

    /**
     * Testimonial::$author
     *
     * @var \O2System\Framework\Libraries\Ui\Components\Card\Body\Author
     */
    public $author;

    public function __construct()
    {
        parent::__construct('div', 'testimonial');
        $this->attributes->addAttributeClass('card-testimonial');
    }

    public function createAuthor()
    {
        $this->author = new Author();

        return $this->author;
    }

    public function render()
    {
        if ($this->paragraph instanceof Element) {
            $this->childNodes->push($this->paragraph);
        }

        $output[] = parent::render();

        if ($this->author instanceof Author) {
            $output[] = $this->author->render();
        }

        return implode(PHP_EOL, $output);
    }
}