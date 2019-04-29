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

use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Label
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class PageHeader extends Element
{
    /**
     * PageHeader::$header
     *
     * @var Element
     */
    public $header;

    /**
     * PageHeader::$subText
     *
     * @var Element
     */
    public $subText;

    // ------------------------------------------------------------------------

    /**
     * PageHeader::__construct
     *
     * @param string|null $header
     * @param string|null $subText
     */
    public function __construct($header = null, $subText = null)
    {
        parent::__construct('div');
        $this->attributes->addAttributeClass('page-header');

        if (isset($header)) {
            $this->setHeader($header);
        }

        if (isset($subText)) {
            $this->setSubText($subText);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * PageHeader::setHeader
     *
     * @param string $text
     * @param string $tagName
     *
     * @return static
     */
    public function setHeader($text, $tagName = 'h1')
    {
        $this->header = new Element($tagName);
        $this->header->entity->setEntityName('header');
        $this->header->textContent->push($text);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * PageHeader::setSubText
     *
     * @param string $text
     *
     * @return static
     */
    public function setSubText($text)
    {
        $this->subText = new Element('small');
        $this->subText->entity->setEntityName('sub-text');
        $this->subText->textContent->push($text);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * PageHeader::render
     *
     * @return string
     */
    public function render()
    {
        $output[] = $this->open();

        if ($this->subText instanceof Element) {
            $this->header->childNodes->push($this->subText);
        }

        $output[] = $this->header;
        $output[] = $this->close();

        return implode(PHP_EOL, $output);
    }
}