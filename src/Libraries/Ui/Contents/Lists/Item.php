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

namespace O2System\Framework\Libraries\Ui\Contents\Lists;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;
use O2System\Framework\Libraries\Ui\Interfaces\ContextualInterface;
use O2System\Framework\Libraries\Ui\Traits\Setters\ContextualClassSetterTrait;
use O2System\Framework\Libraries\Ui\Traits\Setters\HeadingSetterTrait;
use O2System\Framework\Libraries\Ui\Traits\Setters\ParagraphSetterTrait;

/**
 * Class Item
 *
 * @package O2System\Framework\Libraries\Ui\Contents\Lists
 */
class Item extends Element implements ContextualInterface
{
    use ContextualClassSetterTrait;
    use HeadingSetterTrait;
    use ParagraphSetterTrait;

    /**
     * Item::__construct
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct('li');

        if (isset($attributes[ 'id' ])) {
            $this->entity->setEntityName($attributes[ 'id' ]);
        }

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                $this->attributes->addAttribute($name, $value);
            }
        }
    }

    public function active()
    {
        $this->attributes->addAttributeClass('active');
    }

    public function disabled()
    {
        $this->attributes->addAttributeClass('disabled');

        if ($this->childNodes->first() instanceof Item) {
            $this->childNodes->first()->disabled();
        }
    }

    public function render()
    {
        $output[] = $this->open();

        if ( ! empty($this->heading)) {
            $output[] = $this->heading . PHP_EOL;
        }

        if ( ! empty($this->paragraph)) {

            if ($this->textContent->count()) {
                foreach ($this->textContent as $textContent) {
                    $this->paragraph->textContent->push($textContent);
                }
            }

            $output[] = $this->paragraph . PHP_EOL;
        } elseif ($this->textContent->count()) {
            $output[] = implode('', $this->textContent->getArrayCopy());
        }

        if ($this->hasChildNodes()) {

            foreach ($this->childNodes as $childNode) {
                $output[] = $childNode . PHP_EOL;
            }
        }

        $output[] = $this->close();

        return implode('', $output);
    }
}