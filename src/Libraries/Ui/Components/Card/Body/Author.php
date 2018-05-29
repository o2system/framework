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

namespace O2System\Framework\Libraries\Ui\Components\Card\Body;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Contents\Image;
use O2System\Framework\Libraries\Ui\Contents\Link;
use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Author
 * @package O2System\Framework\Libraries\Ui\Components\Card\Body
 */
class Author extends Element
{
    public $photo;
    public $person;
    public $jobTitle;
    public $company;

    public function __construct()
    {
        parent::__construct('div', 'card-author');
        $this->attributes->addAttributeClass('card-author');
    }

    public function setPhoto($src, $href = null)
    {
        if (isset($href)) {
            $this->photo = new Link(new Image($src), $href);
        } else {
            $this->photo = new Image($src);
        }

        return $this;
    }

    public function setPerson($name, $href = null)
    {
        $this->person = new Element('strong', 'person');

        if (isset($href)) {
            $this->person->childNodes->push(new Link($name, $href));
        } else {
            $this->person->textContent->push($name);
        }

        return $this;
    }

    public function setJobTitle($position)
    {
        $this->jobTitle = new Element('small', 'source');
        $this->jobTitle->textContent->push($position);

        return $this;
    }

    public function setCompany($company, $href = null)
    {
        $this->company = new Element('small', 'source');

        if (isset($href)) {
            $this->company->childNodes->push(new Link($company, $href));
        } else {
            $this->company->textContent->push($company);
        }

        return $this;
    }

    public function render()
    {
        // Render Avatar
        if ($this->photo instanceof \O2System\Html\Element) {
            $avatar = new Element('div', 'avatar');
            $avatar->attributes->addAttributeClass('card-avatar');
            $avatar->childNodes->push($this->photo);
            $this->childNodes->push($avatar);
        }

        // Render Profile
        $profile = new Element('div', 'profile');
        $profile->attributes->addAttributeClass('card-profile');
        $profile->childNodes->push($this->person);

        if ($this->jobTitle instanceof \O2System\Html\Element &&
            $this->company instanceof \O2System\Html\Element) {
            $middot = new Element('span', 'middot');
            $middot->textContent->push(' &mdash; ');
            $this->person->childNodes->push($middot);
            $this->person->childNodes->push($this->jobTitle);

            $profile->childNodes->push(new Element('br', 'breakline'));
            $profile->childNodes->push($this->company);
        } else {
            // Render Profile Job Title
            if ($this->jobTitle instanceof \O2System\Html\Element) {
                $profile->childNodes->push(new Element('br', 'breakline'));
                $profile->childNodes->push($this->jobTitle);
            }

            // Render Profile Company
            if ($this->company instanceof \O2System\Html\Element) {
                $profile->childNodes->push(new Element('br', 'breakline'));
                $profile->childNodes->push($this->company);
            }
        }

        $this->childNodes->push($profile);

        // Render clearfix
        $clearfix = new Element('div', 'clearfix');
        $clearfix->clearfix();

        $this->childNodes->push($clearfix);

        return parent::render();
    }
}