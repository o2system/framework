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

/**
 * Class Badge
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class Badge extends Element implements ContextualInterface
{
    use ContextualClassSetterTrait;

    public function __construct($textContent = null, $contextualClass = 'default')
    {
        parent::__construct('span');
        $this->attributes->addAttributeClass('badge');
        $this->setContextualClassPrefix('badge');
        $this->setContextualClassSuffix($contextualClass);

        if (isset($textContent)) {
            $this->setTextContent($textContent);
        }
    }

    public function setTextContent($textContent)
    {
        $this->entity->setEntityName('badge-' . $textContent);
        $this->textContent->push($textContent);

        return $this;
    }
}