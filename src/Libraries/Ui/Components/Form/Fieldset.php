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

use O2System\Framework\Libraries\Ui\Element;
use O2System\Framework\Libraries\Ui\Interfaces\ContextualInterface;
use O2System\Framework\Libraries\Ui\Traits\Setters\ContextualClassSetterTrait;
use O2System\Framework\Libraries\Ui\Traits\Setters\SizingSetterTrait;

/**
 * Class Fieldset
 *
 * @package O2System\Framework\Libraries\Ui\Components\Buttons
 */
class Fieldset extends Element implements ContextualInterface
{
    use Elements\Traits\ElementsCreatorTrait;
    use SizingSetterTrait;
    use ContextualClassSetterTrait;

    public $legend;

    public function __construct(array $attributes = [], $contextualClass = self::DEFAULT_CONTEXT)
    {
        parent::__construct('fieldset');

        if (isset($attributes[ 'id' ])) {
            $this->entity->setEntityName($attributes[ 'id' ]);
        }

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $this->attributes->addAttribute($name, $value);
            }
        }

        $this->attributes->addAttributeClass('form-group');
        $this->attributes->addAttribute('role', 'group');

        // Set input sizing class
        $this->setSizingClassPrefix('form-group');

        // Set contextual class
        $this->setContextualClassPrefix('has');

        if ($contextualClass !== self::DEFAULT_CONTEXT) {
            $this->setContextualClassSuffix($contextualClass);
        }
    }

    /**
     * Fieldset::disabled
     *
     * @return static
     */
    public function disabled()
    {
        $this->attributes->addAttribute('disabled', 'disabled');

        return $this;
    }

    /**
     * Fieldset::createLegend
     *
     * @param string $text
     * @param array  $attributes
     *
     * @return mixed
     */
    public function createLegend($text, array $attributes = [])
    {
        $node = new Fieldset\Legend($attributes);
        $node->entity->setEntityName('legend');
        $node->attributes->addAttribute('for', dash($text));

        $node->textContent->push($text);

        $this->childNodes->prepend($node);

        return $this->legend = $this->childNodes->first();
    }

    // ------------------------------------------------------------------------

    /**
     * Fieldset::createFormGroup
     *
     * @param array $attributes
     *
     * @return \O2System\Framework\Libraries\Ui\Components\Form\Group
     */
    public function createFormGroup(array $attributes = [])
    {
        $this->childNodes->push(new Group($attributes));

        return $this->childNodes->last();
    }
}