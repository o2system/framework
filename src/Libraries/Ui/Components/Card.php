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

use O2System\Framework\Libraries\Ui\Components\Card\Body;
use O2System\Framework\Libraries\Ui\Components\Card\Footer;
use O2System\Framework\Libraries\Ui\Components\Card\Header;
use O2System\Framework\Libraries\Ui\Components\Card\ListGroup;
use O2System\Framework\Libraries\Ui\Element;
use O2System\Framework\Libraries\Ui\Interfaces\ContextualInterface;
use O2System\Framework\Libraries\Ui\Traits\Setters\ContextualClassSetterTrait;
use O2System\Html\Element\Nodes;

/**
 * Class Card
 *
 * @package O2System\Framework\Libraries\Ui\Components
 */
class Card extends Element implements ContextualInterface
{
    use ContextualClassSetterTrait;

    public $header;
    public $ribbons;
    public $badges;
    public $image;
    public $body;
    public $footer;

    public function __construct($contextualClass = self::DEFAULT_CONTEXT, $inverse = false)
    {
        parent::__construct('div', 'card');
        $this->attributes->addAttributeClass('card');

        if ($inverse) {
            $this->setContextualClassPrefix('card');
        } else {
            $this->setContextualClassPrefix('card-outline');
        }

        $this->setContextualClassSuffix($contextualClass);

        $this->header = new Header();
        $this->ribbons = new Nodes();
        $this->badges = new Nodes();
        $this->body = new Body();
        $this->footer = new Footer();
    }

    /**
     * @param string      $src
     * @param string|null $alt
     *
     * @return \O2System\Framework\Libraries\Ui\Components\Card\Image
     */
    public function createImage($src, $alt = null)
    {
        return $this->image = new Card\Image($src, $alt);
    }

    /**
     * @param string      $src
     * @param string|null $alt
     *
     * @return \O2System\Framework\Libraries\Ui\Components\Card\Carousel
     */
    public function createCarousel($id = null)
    {
        return $this->image = new \O2System\Framework\Libraries\Ui\Components\Card\Carousel($id);
    }

    public function createBadge(
        $badge,
        $contextualClass = Card\Badge::DEFAULT_CONTEXT,
        $position = Card\Badge::LEFT_BADGE
    ) {
        if ($badge instanceof Badge) {
            if ( ! isset($badge->position)) {
                $badge->position = $position;
            }
        } elseif (is_string($badge)) {
            $badge = new Card\Badge($badge, $contextualClass, $position);
        }

        $this->badges->push($badge);

        return $this->badges->last();
    }

    public function createRibbon(
        $ribbon,
        $contextualClass = Card\Ribbon::DEFAULT_CONTEXT,
        $position = Card\Ribbon::LEFT_RIBBON
    ) {
        if ($ribbon instanceof Card\Ribbon) {
            $ribbon->position = $position;
        } elseif (is_string($ribbon)) {
            $ribbon = new Card\Ribbon($ribbon, $contextualClass, $position);
        }

        $this->ribbons->push($ribbon);

        return $this->ribbons->last();
    }

    /**
     * @return \O2System\Framework\Libraries\Ui\Components\ListGroup
     */
    public function createListGroup()
    {
        $this->childNodes->push(new ListGroup());

        return $this->childNodes->last();
    }

    /**
     * @return Header
     */
    public function createHeader()
    {
        $this->childNodes->prepend(new Header());

        return $this->header = $this->childNodes->first();
    }

    /**
     * @return Body
     */
    public function createBody()
    {
        $this->childNodes->push(new Body());

        return $this->body = $this->childNodes->last();
    }

    /**
     * @return Footer
     */
    public function createFooter()
    {
        $this->childNodes->push(new Footer());

        return $this->footer = $this->childNodes->last();
    }

    public function render()
    {
        $output[] = $this->open();

        // Render header and image
        if ($this->header->hasTextContent() || $this->header->hasChildNodes()) {
            $output[] = $this->header;
            if ($this->image instanceof \O2System\Framework\Libraries\Ui\Components\Card\Image ||
                $this->image instanceof \O2System\Framework\Libraries\Ui\Components\Card\Carousel
            ) {
                $this->image->attributes->removeAttributeClass('card-img-top');
            }
        } elseif ($this->image instanceof \O2System\Framework\Libraries\Ui\Components\Card\Image ||
            $this->image instanceof \O2System\Framework\Libraries\Ui\Components\Card\Carousel
        ) {
            $this->image->attributes->addAttributeClass('card-img-top');
        }

        // Render ribbons
        if ($this->ribbons->count()) {

            $ribbonsLeft = [];
            $ribbonsRight = [];

            foreach ($this->ribbons as $ribbon) {
                if ($ribbon->position === Card\Ribbon::LEFT_RIBBON) {
                    $ribbonsLeft[] = $ribbon;
                } elseif ($ribbon->position === Card\Ribbon::RIGHT_RIBBON) {
                    $ribbonsRight[] = $ribbon;
                }
            }

            $ribbonContainer = new Element('div');
            $ribbonContainer->attributes->addAttributeClass('card-ribbon');

            if (count($ribbonsLeft)) {
                $ribbonLeftContainer = new Element('div');
                $ribbonLeftContainer->attributes->addAttributeClass(['card-ribbon-container', 'left']);

                foreach ($ribbonsLeft as $ribbonLeft) {
                    $ribbonLeftContainer->childNodes->push($ribbonLeft);
                }

                $ribbonContainer->childNodes->push($ribbonLeftContainer);
            }

            if (count($ribbonsRight)) {
                $ribbonRightContainer = new Element('div');
                $ribbonRightContainer->attributes->addAttributeClass(['card-ribbon-container', 'right']);

                foreach ($ribbonsRight as $ribbonRight) {
                    $ribbonRightContainer->childNodes->push($ribbonRight);
                }

                $ribbonContainer->childNodes->push($ribbonRightContainer);
            }

            $output[] = $ribbonContainer;
        }

        // Render images
        if ($this->image instanceof \O2System\Framework\Libraries\Ui\Components\Card\Image ||
            $this->image instanceof \O2System\Framework\Libraries\Ui\Components\Card\Carousel
        ) {
            $output[] = $this->image;
        }

        // Render badges
        if ($this->badges->count()) {

            $badgesLeft = [];
            $badgesRight = [];
            $badgesInline = [];

            foreach ($this->badges as $badge) {
                if ($badge->position === Card\Badge::LEFT_BADGE) {
                    $badgesLeft[] = $badge;
                } elseif ($badge->position === Card\Badge::RIGHT_BADGE) {
                    $badgesRight[] = $badge;
                } elseif ($badge->position === Card\Badge::INLINE_BADGE) {
                    $badgesInline[] = $badge;
                }
            }

            $badgeContainer = new Element('div');
            $badgeContainer->attributes->addAttributeClass('card-badge');

            if (count($badgesLeft)) {
                $badgeLeftContainer = new Element('div');
                $badgeLeftContainer->attributes->addAttributeClass(['card-badge-container', 'left']);

                foreach ($badgesLeft as $badgeLeft) {
                    $badgeLeftContainer->childNodes->push($badgeLeft);
                }

                $badgeContainer->childNodes->push($badgeLeftContainer);
            }

            if (count($badgesRight)) {
                $badgeRightContainer = new Element('div');
                $badgeRightContainer->attributes->addAttributeClass(['card-badge-container', 'right']);

                foreach ($badgesRight as $badgeRight) {
                    $badgeRightContainer->childNodes->push($badgeRight);
                }

                $badgeContainer->childNodes->push($badgeRightContainer);
            }

            if (count($badgesInline)) {
                $badgeInlineContainer = new Element('div');
                $badgeInlineContainer->attributes->addAttributeClass(['card-badge-container', 'inline']);

                foreach ($badgesInline as $badgeInline) {
                    $badgeInlineContainer->childNodes->push($badgeInline);
                }

                $badgeContainer->childNodes->push($badgeInlineContainer);
            }

            $output[] = $badgeContainer;
        }

        // Render body
        if ($this->hasChildNodes()) {
            $output[] = implode(PHP_EOL, $this->childNodes->getArrayCopy());
        }

        if ($this->hasTextContent()) {
            $output[] = implode(PHP_EOL, $this->textContent->getArrayCopy());
        }

        // Render footer
        if ($this->footer->hasTextContent() || $this->footer->hasChildNodes()) {
            $output[] = $this->footer;
        }

        $output[] = $this->close();

        return implode(PHP_EOL, $output);
    }
}