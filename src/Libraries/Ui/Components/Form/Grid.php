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
use O2System\Framework\Libraries\Ui\Grid\Row;

/**
 * Class Grid
 * @package O2System\Framework\Libraries\Ui\Components\Form
 */
class Grid extends Row
{
    use ElementsCreatorTrait;

    /**
     * Grid::__construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->attributes->removeAttributeClass('row');
        $this->attributes->addAttributeClass('form-row');
    }

    /**
     * Grid::createFromGroup
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