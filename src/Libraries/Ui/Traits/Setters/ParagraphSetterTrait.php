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

use O2System\Framework\Libraries\Ui\Contents\Paragraph;

/**
 * Trait ParagraphSetterTrait
 *
 * @package O2System\Framework\Libraries\Ui\Traits\Setters
 */
trait ParagraphSetterTrait
{
    public $paragraph;

    public function setParagraph($text)
    {
        $this->paragraph = new Paragraph();
        $this->paragraph->entity->setEntityName('paragraph');
        $this->paragraph->textContent->push($text);

        return $this;
    }
}