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
    public $inline = false;

    public function unstyled()
    {
        $this->attributes->removeAttributeClass('list-*');
        $this->attributes->addAttributeClass('list-unstyled');

        return $this;
    }

    public function inline($inline = true)
    {
        $this->inline = (bool)$inline;

        return $this;
    }

    public function createLists(array $lists)
    {
        if (count($lists)) {
            foreach ($lists as $list) {
                $this->createList($list);
            }
        }

        return $this;
    }

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

    protected function pushChildNode(Element $node)
    {
        if ($node->hasChildNodes()) {
            if ($node->childNodes->first() instanceof Link) {

                $parseUrl = parse_url($node->childNodes->first()->getAttributeHref());
                $parseUrlQuery = [];

                if (isset($parseUrl[ 'query' ])) {
                    parse_str($parseUrl[ 'query' ], $parseUrlQuery);
                }

                if (isset($parseUrlQuery[ 'page' ])) {
                    if (input()->get('page') === $parseUrlQuery[ 'page' ]) {
                        $node->attributes->addAttributeClass('active');
                        $node->childNodes->first()->attributes->addAttributeClass('active');
                    }
                } else {
                    $hrefUriSegments = [];

                    if (isset($parseUrl[ 'path' ])) {
                        $hrefUriSegments = (new Uri\Segments($parseUrl[ 'path' ]))->getParts();
                    }

                    $currentUriSegments = server_request()->getUri()->getSegments()->getParts();

                    $matchSegments = array_slice($currentUriSegments, 0, count($hrefUriSegments));

                    $stringHrefSegments = implode('/', $hrefUriSegments);
                    $stringMatchSegments = implode('/', $matchSegments);

                    if ($stringHrefSegments === $stringMatchSegments) {
                        $node->attributes->addAttributeClass('active');
                        $node->childNodes->first()->attributes->addAttributeClass('active');
                    }
                }
            }
        }

        $this->childNodes->push($node);
    }

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