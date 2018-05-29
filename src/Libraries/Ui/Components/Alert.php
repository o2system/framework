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

namespace O2System\Framework\Libraries\Ui\Components;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;
use O2System\Framework\Libraries\Ui\Interfaces\ContextualInterface;
use O2System\Framework\Libraries\Ui\Traits\Setters\ContextualClassSetterTrait;
use O2System\Framework\Libraries\Ui\Traits\Setters\HeadingSetterTrait;

/**
 * Class Label
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class Alert extends Element implements ContextualInterface
{
    use ContextualClassSetterTrait;
    use HeadingSetterTrait;

    protected $dismissible = false;

    public function __construct($textContent = null, $contextualClass = 'default')
    {
        parent::__construct('div');
        $this->attributes->addAttributeClass('alert');
        $this->attributes->addAttribute('role', 'alert');

        if (isset($textContent)) {
            $this->setText($textContent);
        }

        $this->setContextualClassPrefix('alert');
        $this->setContextualClassSuffix($contextualClass);
    }

    public function setText($text)
    {
        $this->entity->setEntityName($text);
        $this->textContent->push($text);

        return $this;
    }

    public function dismissible()
    {
        $this->dismissible = true;

        return $this;
    }

    public function render()
    {
        if ($this->dismissible) {
            $this->attributes->addAttributeClass('alert-dismissible');

            $button = new Element('button');
            $button->entity->setEntityName('button-dismiss');
            $button->attributes->addAttribute('type', 'button');
            $button->attributes->addAttributeClass('close');
            $button->attributes->addAttribute('data-dismiss', 'alert');
            $button->attributes->addAttribute('aria-label', 'close');

            $icon = new Element('span');
            $icon->entity->setEntityName('button-dismiss-icon');
            $icon->attributes->addAttribute('aria-hidden', true);
            $icon->textContent->push('&times;');

            $button->childNodes->push($icon);
        }

        $output[] = $this->open();

        if (isset($button)) {
            $output[] = $button;
        }

        if ($this->heading instanceof Element) {
            $this->heading->tagName = 'h4';
            $this->heading->attributes->addAttributeClass('alert-heading');
            $output[] = $this->heading;
        }

        if ($this->textContent->count()) {
            $content = PHP_EOL . implode('', $this->textContent->getArrayCopy()) . PHP_EOL;

            if ($this->heading instanceof Element) {
                $content = '<p>' . $content . '</p>';
            }

            $DOMDocument = new \DOMDocument();
            libxml_use_internal_errors(true);
            $DOMDocument->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();
            $links = $DOMDocument->getElementsByTagName('a');

            if ($links->length > 0) {
                foreach ($links as $link) {
                    $class = $link->getAttribute('class');
                    $class = $class . ' alert-link';

                    $link->setAttribute('class', trim($class));
                }

                $content = $DOMDocument->saveHTML();
            }

            $output[] = $content;
        }

        if ($this->hasChildNodes()) {
            if ($this->textContent->count() == 0) {
                $output[] = PHP_EOL;
            }

            foreach ($this->childNodes as $childNode) {

                if ($childNode instanceof Link) {
                    $childNode->attributes->addAttributeClass('alert-link');
                }

                $output[] = $childNode . PHP_EOL;
            }
        }

        $output[] = $this->close();

        return implode(PHP_EOL, $output);
    }
}