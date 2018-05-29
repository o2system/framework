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

use O2System\Framework\Libraries\Ui\Contents\Link;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Framework\Libraries\Ui\Element;
use O2System\Framework\Libraries\Ui\Traits\Setters\SizingSetterTrait;

/**
 * Class Pagination
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class Pagination extends Unordered
{
    use SizingSetterTrait;

    protected $total = 0;
    protected $entries = 5;
    protected $pages = 0;

    public function __construct($total = 0, $entries = 5)
    {
        parent::__construct();

        $this->attributes->addAttributeClass('pagination');
        $this->setTotal($total);
        $this->setEntries($entries);

        $this->setSizingClassPrefix('pagination');
    }

    public function setTotal($total)
    {
        $this->total = (int)$total;
        $this->setPages(ceil($total / $this->entries));

        return $this;
    }

    public function setPages($pages)
    {
        $this->pages = (int)$pages;

        return $this;
    }

    public function setEntries($entries)
    {
        $this->entries = (int)$entries;

        if ($this->total > 0) {
            $this->setPages(ceil($this->total / $entries));
        }

        return $this;
    }

    public function render()
    {
        // returning empty string if the num pages is zero
        if ($this->pages == 0 || $this->pages == 1) {
            return '';
        }

        $output[] = $this->open() . PHP_EOL;

        $current = (int)input()->get('page');
        $current = $current == 0 ? 1 : $current;

        $previous = new Element('span');
        $previous->entity->setEntityName('previous');
        $previous->attributes->addAttribute('aria-hidden', true);
        $previous->textContent->push('&laquo;');

        if ($current == 1) {
            $this->createList(new Link($previous, '#'));
            $this->childNodes->current()->disabled();
        } else {
            $this->createList(new Link($previous, current_url('', ['page' => $current - 1])));
        }

        foreach (range(1, $this->pages) as $page) {
            $this->createList(new Link($page, current_url('', ['page' => $page])));

            if ($current == $page) {

                $currentNode = $this->childNodes->current();
                $currentNode->active();

                $srOnly = new Element('span');
                $srOnly->entity->setEntityName('current');
                $srOnly->attributes->addAttributeClass('sr-only');
                $srOnly->textContent->push('(current)');

                $currentNode->childNodes->first()->childNodes->push($srOnly);
            }
        }

        $next = new Element('span');
        $next->entity->setEntityName('next');
        $next->attributes->addAttribute('aria-hidden', true);
        $next->textContent->push('&raquo;');

        if ($current == $this->pages) {
            $this->createList(new Link($next, '#'));
            $this->childNodes->current()->disabled();
        } else {
            $this->createList(new Link($next, current_url('', ['page' => $current + 1])));
        }

        if ($this->hasChildNodes()) {
            $output[] = implode(PHP_EOL, $this->childNodes->getArrayCopy());
        }

        $output[] = PHP_EOL . $this->close();

        return implode('', $output);
    }

    protected function pushChildNode(Element $node)
    {
        $node->attributes->addAttributeClass('page-item');

        if ($node->childNodes->first() instanceof Link) {
            $node->childNodes->first()->attributes->addAttributeClass('page-link');
        }

        parent::pushChildNode($node);
    }
}