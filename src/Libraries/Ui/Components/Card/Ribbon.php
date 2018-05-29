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

use O2System\Framework\Libraries\Ui\Element;
use O2System\Framework\Libraries\Ui\Interfaces\ContextualInterface;
use O2System\Framework\Libraries\Ui\Traits\Setters\ContextualClassSetterTrait;

/**
 * Class Ribbon
 * @package O2System\Framework\Libraries\Ui\Components\Card
 */
class Ribbon extends Element implements ContextualInterface
{
    use ContextualClassSetterTrait;

    const LEFT_RIBBON = 0;
    const RIGHT_RIBBON = 1;

    public $position;

    public function __construct($textContent = null, $contextualClass = 'default', $position = self::LEFT_RIBBON)
    {
        parent::__construct('span');
        $this->attributes->addAttributeClass('ribbon');
        $this->setContextualClassPrefix('ribbon');
        $this->setContextualClassSuffix($contextualClass);

        if (isset($textContent)) {
            $this->setTextContent($textContent);
        }

        $this->position = $position;
    }

    public function setTextContent($textContent)
    {
        $this->entity->setEntityName('ribbon-' . $textContent);
        $this->textContent->push($textContent);

        return $this;
    }
}