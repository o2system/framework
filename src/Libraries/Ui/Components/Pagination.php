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

    /**
     * Pagination::$total
     *
     * @var int
     */
    protected $total = 0;

    /**
     * Pagination::$limit
     *
     * @var int
     */
    protected $limit = 5;

    /**
     * Pagination::$pages
     *
     * @var int
     */
    protected $pages = 0;

    // ------------------------------------------------------------------------

    /**
     * Pagination::__construct
     *
     * @param int $total
     * @param int $limit
     */
    public function __construct($total = 0, $limit = 5)
    {
        parent::__construct();

        $this->attributes->addAttributeClass('pagination');
        $this->setTotal($total);
        $this->setLimit($limit);

        $this->setSizingClassPrefix('pagination');
    }

    // ------------------------------------------------------------------------

    /**
     * Pagination::setTotal
     *
     * @param int $total
     *
     * @return static
     */
    public function setTotal($total)
    {
        $this->total = (int)$total;
        $this->setPages(ceil($total / $this->limit));

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Pagination::setPages
     *
     * @param int $pages
     *
     * @return static
     */
    public function setPages($pages)
    {
        $this->pages = (int)$pages;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Pagination::setLimit
     *
     * @param int $limit
     *
     * @return static
     */
    public function setLimit($limit)
    {
        $this->limit = (int)$limit;

        if ($this->total > 0 && $this->limit > 0) {
            $this->setPages(ceil($this->total / $limit));
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Pagination::render
     *
     * @return string
     */
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

    // ------------------------------------------------------------------------

    /**
     * Pagination::pushChildNode
     *
     * @param \O2System\Framework\Libraries\Ui\Element $node
     */
    protected function pushChildNode(Element $node)
    {
        $node->attributes->addAttributeClass('page-item');

        if ($node->childNodes->first() instanceof Link) {
            $node->childNodes->first()->attributes->addAttributeClass('page-link');
        }

        parent::pushChildNode($node);
    }
}