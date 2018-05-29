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

namespace O2System\Framework\Libraries\Ui\Traits\Setters;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;

/**
 * Trait TitleSetterTrait
 *
 * @package O2System\Framework\Libraries\Ui\Traits\Setters
 */
trait TitleSetterTrait
{
    public $title;
    public $subTitle;

    public function setTitle($text, $tagName = 'h4')
    {
        $this->title = new Element($tagName, 'title');
        $this->title->entity->setEntityName($text);
        $this->title->textContent->push($text);

        return $this;
    }

    public function setSubTitle($text, $tagName = 'h6')
    {
        $this->subTitle = new Element($tagName, 'subTitle');
        $this->subTitle->entity->setEntityName($text);
        $this->subTitle->textContent->push($text);

        return $this;
    }
}