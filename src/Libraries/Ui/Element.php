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

namespace O2System\Framework\Libraries\Ui;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Traits\Utilities;
use O2System\Html;

/**
 * Class Element
 * @package O2System\Framework\Libraries\Ui
 */
class Element extends Html\Element
{
    use Utilities\BorderUtilitiesTrait;
    use Utilities\ColorUtilitiesTrait;
    use Utilities\DisplayUtilitiesTrait;
    use Utilities\FloatUtilitiesTrait;
    use Utilities\PositionUtilitiesTrait;
    use Utilities\ShapeUtilitiesTrait;
    use Utilities\SizingUtilitiesTrait;
    use Utilities\SpacingUtilitiesTrait;
    use Utilities\TextUtilitiesTrait;
    use Utilities\VerticalAlignmentUtilitiesTrait;

    // ------------------------------------------------------------------------

    public function clearfix()
    {
        $this->attributes->addAttributeClass('clearfix');

        return $this;
    }

    // ------------------------------------------------------------------------

    public function embedResponsive($ratio = null)
    {
        $this->attributes->addAttributeClass('embed-responsive');

        $ratio = empty($ratio) ? '1:1' : $ratio;

        switch ($ratio) {
            default:
            case '1:1':
                $this->attributes->addAttributeClass('embed-responsive-1by1');
                break;

            case '21:9':
                $this->attributes->addAttributeClass('embed-responsive-21by9');
                break;

            case '16:9':
                $this->attributes->addAttributeClass('embed-responsive-16by9');
                break;

            case '4:3':
                $this->attributes->addAttributeClass('embed-responsive-4by3');
                break;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    public function screenReaderOnly($focusable = false)
    {
        $this->attributes->addAttributeClass('sr-only');

        if ($focusable) {
            $this->attributes->addAttributeClass('sr-only-focusable');
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    public function visible($visible = true)
    {
        $this->attributes->addAttributeClass(($visible === true ? 'visible' : 'invisible'));

        return $this;
    }
}