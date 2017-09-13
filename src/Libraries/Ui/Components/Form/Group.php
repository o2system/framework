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

use O2System\Framework\Libraries\Ui\Interfaces\ContextualInterface;
use O2System\Framework\Libraries\Ui\Traits\Setters\ContextualClassSetterTrait;
use O2System\Framework\Libraries\Ui\Traits\Setters\SizingSetterTrait;
use O2System\Html\Element;

/**
 * Class Group
 *
 * @package O2System\Framework\Libraries\Ui\Components\Buttons
 */
class Group extends Element implements ContextualInterface
{
    use ContextualClassSetterTrait;
    use SizingSetterTrait;

    public $label;
    public $input;
    public $help;
    protected $columns;

    public function __construct( $contextualClass = self::DEFAULT_CONTEXT )
    {
        parent::__construct( 'div' );

        $this->setSizingClassPrefix( 'form-group' );
        $this->attributes->addAttributeClass( 'form-group' );

        $this->attributes->addAttribute( 'role', 'group' );

        $this->setContextualClassPrefix( 'has' );
        if ( $contextualClass !== self::DEFAULT_CONTEXT ) {
            $this->setContextualClassSuffix( $contextualClass );
        }
    }

    public function setColumns( $labelColumn, $fieldColumn = null )
    {
        if ( empty( $fieldColumn ) ) {
            $fieldColumn = 12 - (int)$labelColumn;
            $fieldColumn = $fieldColumn < 0 ? 0 : $fieldColumn;
        }

        $this->columns = [
            'label' => (int)$labelColumn,
            'field' => (int)$fieldColumn,
        ];
    }

    /**
     * @param       $label
     * @param array $attributes
     *
     * @return \O2System\Framework\Libraries\Ui\Components\Form\Label
     */
    public function createLabel( $label, array $attributes = [] )
    {
        $element = new Label( 'label', 'label-' . $label );
        $element->attributes->addAttribute( 'for', dash( $label ) );

        if ( count( $attributes ) ) {
            foreach ( $attributes as $name => $value ) {
                $element->attributes->addAttribute( $name, $value );
            }
        }

        $element->textContent->push( $label );

        $this->childNodes->push( $element );

        return $this->label = $this->childNodes->last();
    }

    /**
     * @param array $attributes
     *
     * @return \O2System\Framework\Libraries\Ui\Components\Form\Input
     */
    public function createInput( array $attributes = [] )
    {
        $field = new Input();

        if ( count( $attributes ) ) {
            foreach ( $attributes as $name => $value ) {
                $field->attributes->addAttribute( $name, $value );

                if ( $name === 'name' ) {
                    $this->entity->setEntityName( 'input-' . $value );

                    if ( ! array_key_exists( 'id', $attributes ) ) {
                        $field->attributes->setAttributeId( 'input-' . $value );
                    }

                    if ( $this->label instanceof Element ) {
                        $this->label->attributes->addAttribute( 'for', $value );
                    }
                }
            }
        }

        $this->childNodes->push( $field );

        return $this->input = $this->childNodes->last();
    }

    /**
     * @param array $attributes
     *
     * @return \O2System\Framework\Libraries\Ui\Components\Form\Input\Group
     */
    public function createInputGroup( array $attributes = [] )
    {
        $inputGroup = new Input\Group();

        if ( count( $attributes ) ) {
            $inputGroup->createInput( $attributes );
        }

        $this->childNodes->push( $inputGroup );

        return $this->input = $this->childNodes->last();
    }

    /**
     * @param array $attributes
     *
     * @return \O2System\Framework\Libraries\Ui\Components\Form\Textarea
     */
    public function createTextarea( array $attributes = [] )
    {
        $field = new Textarea();

        if ( count( $attributes ) ) {
            foreach ( $attributes as $name => $value ) {
                $field->attributes->addAttribute( $name, $value );

                if ( $name === 'name' ) {
                    $this->entity->setEntityName( 'textarea-' . $value );

                    if ( ! array_key_exists( 'id', $attributes ) ) {
                        $field->attributes->setAttributeId( 'textarea-' . $value );
                    }

                    if ( $this->label instanceof Element ) {
                        $this->label->attributes->addAttribute( 'for', $value );
                    }
                }
            }
        }

        $this->childNodes->push( $field );

        return $this->input = $this->childNodes->last();
    }

    /**
     * @param array $options
     * @param null  $selected
     * @param array $attributes
     *
     * @return \O2System\Framework\Libraries\Ui\Components\Form\Select
     */
    public function createSelect( array $options = [], $selected = null, array $attributes = [] )
    {
        $select = new Select();

        if ( count( $attributes ) ) {
            foreach ( $attributes as $name => $value ) {
                $select->attributes->addAttribute( $name, $value );

                if ( $name === 'name' ) {
                    $this->entity->setEntityName( 'select-' . $value );

                    if ( ! array_key_exists( 'id', $attributes ) ) {
                        $select->attributes->setAttributeId( 'select-' . $value );
                    }

                    if ( $this->label instanceof Element ) {
                        $this->label->attributes->addAttribute( 'for', $value );
                    }
                }
            }
        }

        if ( count( $options ) ) {
            $select->createOptions( $options, $selected );
        }

        $this->childNodes->push( $select );

        return $this->input = $this->childNodes->last();
    }

    /**
     * @param null   $text
     * @param string $tagName
     *
     * @return \O2System\Framework\Libraries\Ui\Components\Form\Group\Help
     */
    public function createHelp( $text = null, $tagName = 'span' )
    {
        $help = new Group\Help( $tagName );

        if ( isset( $text ) ) {
            $help->textContent->push( $text );
        }

        if ( $this->input instanceof Input ) {
            $help->attributes->setAttributeId(
                $helpId = str_replace(
                    'field',
                    'help',
                    $this->input->attributes->getAttributeId()
                )
            );

            $this->input->attributes->addAttribute( 'aria-describedby', $helpId );
        }

        $this->childNodes->push( $help );

        return $this->help = $this->childNodes->last();
    }

    protected function renderCheckbox()
    {
        $output[] = $this->open();

        $label = new Element( 'label', $this->label->entity->getEntityName() );
        $label->attributes->addAttributeClass( [ 'custom-control', 'custom-checkbox' ] );

        $field = new Element( 'input', $this->input->entity->getEntityName() );
        $field->attributes = $this->input->attributes;
        $field->attributes->addAttributeClass( 'custom-control-input' );

        $label->childNodes->push( $field );

        $indicator = new Element( 'span', 'indicator-' . $this->label->entity->getEntityName() );
        $indicator->attributes->addAttributeClass( 'custom-control-indicator' );

        $label->childNodes->push( $indicator );

        $description = new Element( 'span', 'description-' . $this->label->entity->getEntityName() );
        $description->attributes->addAttributeClass( 'custom-control-description' );
        $description->textContent = $this->label->textContent;

        $label->childNodes->push( $description );

        $output[] = $label;

        $output[] = $this->close();

        return implode( PHP_EOL, $output );
    }

    protected function renderRadio()
    {
        $output[] = $this->open();

        $label = new Element( 'label', $this->label->entity->getEntityName() );
        $label->attributes->addAttributeClass( [ 'custom-control', 'custom-radio' ] );

        $field = new Element( 'input', $this->input->entity->getEntityName() );
        $field->attributes = $this->input->attributes;
        $field->attributes->addAttributeClass( 'custom-control-input' );

        $label->childNodes->push( $field );

        $indicator = new Element( 'span', 'indicator-' . $this->label->entity->getEntityName() );
        $indicator->attributes->addAttributeClass( 'custom-control-indicator' );

        $label->childNodes->push( $indicator );

        $description = new Element( 'span', 'description-' . $this->label->entity->getEntityName() );
        $description->attributes->addAttributeClass( 'custom-control-description' );
        $description->textContent = $this->label->textContent;

        $label->childNodes->push( $description );

        $output[] = $label;

        $output[] = $this->close();

        return implode( PHP_EOL, $output );
    }

    protected function renderFile()
    {
        $output[] = $this->open();

        $label = new Element( 'label', $this->label->entity->getEntityName() );
        $label->attributes->addAttributeClass( 'custom-file' );

        $field = new Element( 'input', $this->input->entity->getEntityName() );
        $field->attributes = $this->input->attributes;
        $field->attributes->addAttributeClass( 'custom-file-input' );

        $label->childNodes->push( $field );

        $control = new Element( 'span', 'control-' . $this->label->entity->getEntityName() );
        $control->attributes->addAttributeClass( 'custom-file-control' );

        $label->childNodes->push( $control );

        $output[] = $label;

        $output[] = $this->close();

        return implode( PHP_EOL, $output );
    }

    public function render()
    {
        if ( $this->input instanceof Element ) {

            if ( false !== ( $hasClass = $this->attributes->findAttributeClass( 'has-' ) ) ) {
                $hasClass = reset( $hasClass );
                $this->input->attributes->addAttributeClass( str_replace( 'has', 'form-control', $hasClass ) );
            }

            if ( false !== ( $type = $this->input->attributes->getAttribute( 'type' ) ) ) {
                if ( method_exists( $this, $methodName = 'render' . studlycase( $type ) ) ) {
                    return call_user_func( [ $this, $methodName ] );
                }
            }
        }

        $output[] = $this->open();

        // Label
        if ( $this->label instanceof Element ) {
            $output[] = $this->label;
        }

        // Input
        if ( $this->input instanceof Element ) {
            $output[] = $this->input;
        }

        // Help
        if ( $this->help instanceof Element ) {
            $output[] = $this->help;
        }

        $output[] = $this->close();


        return implode( PHP_EOL, $output );
    }
}