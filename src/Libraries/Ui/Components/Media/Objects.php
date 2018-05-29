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

namespace O2System\Framework\Libraries\Ui\Components\Media;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components\Lists\Item;
use O2System\Framework\Libraries\Ui\Element;
use O2System\Framework\Libraries\Ui\Traits\Setters\HeadingSetterTrait;
use O2System\Framework\Libraries\Ui\Traits\Setters\ImageSetterTrait;
use O2System\Framework\Libraries\Ui\Traits\Setters\ParagraphSetterTrait;

/**
 * Class Objects
 *
 * @package O2System\Framework\Libraries\Ui\Components\Media
 */
class Objects extends Element
{
    use ImageSetterTrait;
    use HeadingSetterTrait;
    use ParagraphSetterTrait;

    public $body;
    public $alignment;

    public function __construct()
    {
        parent::__construct('div');
        $this->attributes->addAttributeClass('media');

        $this->body = new Element('div');
        $this->body->attributes->addAttributeClass('media-body');
    }

    public function alignTop()
    {
        $this->alignment = 'TOP';

        return $this;
    }

    public function alignMiddle()
    {
        $this->alignment = 'MIDDLE';

        return $this;
    }

    public function alignBottom()
    {
        $this->alignment = 'BOTTOM';

        return $this;
    }

    public function createNestedObject($list = null)
    {
        $node = new Objects();
        $node->tagName = 'div';

        if ($list instanceof Objects) {
            $node = $list;
        } elseif ($list instanceof Element) {
            $node->entity->setEntityName($list->entity->getEntityName());
            $node->childNodes->push($list);
        } else {
            $node->entity->setEntityName('media-nested-' . ($this->childNodes->count() + 1));

            if (isset($list)) {
                $node->entity->setEntityName($list);
                $node->textContent->push($list);
            }
        }

        $this->childNodes->push($node);

        return $this->childNodes->last();
    }

    public function render()
    {
        $output[] = $this->open();

        if ($this->image instanceof Element) {
            $this->image->attributes->addAttributeClass(['mr-3']);

            if ($this->alignment === 'TOP') {
                $this->image->attributes->addAttributeClass(['align-self-start']);
            } elseif ($this->alignment === 'MIDDLE') {
                $this->image->attributes->addAttributeClass(['align-self-center']);
            } elseif ($this->alignment === 'BOTTOM') {
                $this->image->attributes->addAttributeClass(['align-self-end']);
            }

            $output[] = $this->image;
        }

        if ($this->paragraph instanceof Element) {
            $this->body->childNodes->prepend($this->paragraph);
        }

        if ($this->hasChildNodes()) {
            foreach ($this->childNodes as $childNode) {
                $this->body->childNodes->push($childNode);
            }
        }

        if ($this->heading instanceof Element) {
            $this->heading->tagName = 'h4';
            $this->heading->attributes->addAttributeClass(['mt-0']);
            $this->body->childNodes->prepend($this->heading);
        }

        $output[] = $this->body;

        $output[] = $this->close();

        return implode(PHP_EOL, $output);
    }
}