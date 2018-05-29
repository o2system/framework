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

use O2System\Framework\Libraries\Ui\Components\Media\Objects;
use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Media
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class Media extends Element
{
    public function __construct()
    {
        parent::__construct('div');
        $this->attributes->addAttributeClass('media-list');
    }

    public function createObject($list = null)
    {
        $node = new Objects();

        if ($list instanceof Objects) {
            $node = $list;
        } elseif ($list instanceof Element) {
            $node->entity->setEntityName($list->entity->getEntityName());
            $node->childNodes->push($list);
        } else {
            $node->entity->setEntityName('media-' . ($this->childNodes->count() + 1));

            if (isset($list)) {
                $node->entity->setEntityName($list);
                $node->textContent->push($list);
            }
        }

        $this->childNodes->push($node);

        return $this->childNodes->last();
    }
}