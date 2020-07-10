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

namespace O2System\Framework\Libraries\Ui\Contents\Lists\Abstracts;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Contents\Link;
use O2System\Framework\Libraries\Ui\Contents\Lists\Item;
use O2System\Framework\Libraries\Ui\Element;
use O2System\Kernel\Http\Message\Uri;

/**
 * Class AbstractList
 *
 * @package O2System\Framework\Libraries\Ui\Abstracts
 */
abstract class AbstractList extends Element
{
    /**
     * AbstractList::$inline
     *
     * @var bool
     */
    public $inline = false;

    // ------------------------------------------------------------------------

    /**
     * AbstractList::unstyled
     *
     * @return static
     */
    public function unstyled()
    {
        $this->attributes->removeAttributeClass('list-*');
        $this->attributes->addAttributeClass('list-unstyled');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractList::inline
     *
     * @param bool $inline
     *
     * @return static
     */
    public function inline($inline = true)
    {
        $this->inline = (bool)$inline;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractList::createLists
     *
     * @param array $lists
     *
     * @return static
     */
    public function createLists(array $lists)
    {
        if (count($lists)) {
            foreach ($lists as $list) {
                $this->createList($list);
            }
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractList
     *
     * @param Item|Element|int|string|null $list
     *
     * @return Item
     */
    public function createList($list = null)
    {
        $node = new Item();

        if ($list instanceof Item) {
            $node = $list;
        } elseif ($list instanceof Element) {
            $node->entity->setEntityName($list->entity->getEntityName());
            $node->childNodes->push($list);
        } else {
            if (is_numeric($list)) {
                $node->entity->setEntityName('list-' . $list);
            }

            if (isset($list)) {
                $node->entity->setEntityName($list);
                $node->textContent->push($list);
            }
        }

        $this->pushChildNode($node);

        return $this->childNodes->last();
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractList::pushChildNode
     *
     * @param \O2System\Framework\Libraries\Ui\Element $node
     */
    protected function pushChildNode(Element $node)
    {
        if ($node->hasChildNodes()) {
            if ($node->childNodes->first() instanceof Link) {
                $href = $node->childNodes->first()->getAttributeHref();
                $segments = [];

                if(strpos($href, 'http') === false) {
                    $segments = explode('/', $href);
                }

                if(server_request()->getUri()->segments->has(reset($segments)) && server_request()->getUri()->segments->has(end($segments))) {
                    $link->attributes->addAttributeClass('active');
                }
            }
        }

        $this->childNodes->push($node);
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractList::render
     *
     * @return string
     */
    public function render()
    {
        $output[] = $this->open();

        if ($this->hasChildNodes()) {
            if ($this->inline) {
                $this->attributes->addAttributeClass('list-inline');
            }

            foreach ($this->childNodes as $childNode) {
                if ($this->inline) {
                    $childNode->attributes->addAttributeClass('list-inline-item');
                }
                $output[] = $childNode;
            }
        }

        $output[] = $this->close();

        return implode(PHP_EOL, $output);
    }
}