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

namespace O2System\Framework\Libraries\Ui\Components\Form;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components\Form\Elements\Traits\ElementsCreatorTrait;
use O2System\Framework\Libraries\Ui\Element;
use O2System\Framework\Libraries\Ui\Interfaces\ContextualInterface;
use O2System\Framework\Libraries\Ui\Traits\Setters\ContextualClassSetterTrait;
use O2System\Framework\Libraries\Ui\Traits\Setters\SizingSetterTrait;

/**
 * Class Group
 *
 * @package O2System\Framework\Libraries\Ui\Components\Buttons
 */
class Group extends Element implements ContextualInterface
{
    use ElementsCreatorTrait;
    use ContextualClassSetterTrait;
    use SizingSetterTrait;

    public $help;

    public function __construct(array $attributes = [], $contextualClass = self::DEFAULT_CONTEXT)
    {
        parent::__construct('div');

        if (isset($attributes[ 'id' ])) {
            $this->entity->setEntityName($attributes[ 'id' ]);
        }

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $this->attributes->addAttribute($name, $value);
            }
        }

        $this->setSizingClassPrefix('form-group');
        $this->attributes->addAttributeClass('form-group');

        $this->attributes->addAttribute('role', 'group');

        $this->setContextualClassPrefix('has');
        if ($contextualClass !== self::DEFAULT_CONTEXT) {
            $this->setContextualClassSuffix($contextualClass);
        }
    }

    /**
     * @param null   $text
     * @param string $tagName
     *
     * @return \O2System\Framework\Libraries\Ui\Components\Form\Group\Help
     */
    public function createHelp($text = null, $tagName = 'span')
    {
        $help = new Group\Help($tagName);

        if (isset($text)) {
            $help->textContent->push($text);
        }

        $this->childNodes->push($help);

        return $this->help = $this->childNodes->last();
    }

    public function render()
    {
        if ($this->help instanceof Group\Help) {
            foreach ($this->childNodes as $childNode) {
                if ($childNode instanceof Elements\Input or
                    $childNode instanceof Elements\Checkbox or
                    $childNode instanceof Elements\Select or
                    $childNode instanceof Elements\Textarea
                ) {
                    if (false !== ($attributeId = $childNode->attributes->getAttributeId())) {
                        $this->help->attributes->setAttributeId('help-' . $attributeId);
                        $childNode->attributes->addAttribute('aria-describedby', 'help-' . $attributeId);
                    }
                }
            }
        }

        return parent::render();
    }
}