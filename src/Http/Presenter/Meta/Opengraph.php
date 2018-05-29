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

namespace O2System\Framework\Http\Presenter\Meta;

// ------------------------------------------------------------------------

use O2System\Html\Element;
use O2System\Psr\Patterns\Structural\Repository\AbstractRepository;

/**
 * Class Opengraph
 *
 * @package O2System\Framework\Http\Presenter\Meta
 */
class Opengraph extends AbstractRepository
{
    public $prefix;

    public function __construct()
    {
        $this->prefix = 'http://ogp.me/ns#';
    }

    public function setNamespace(Opengraph\Abstracts\AbstractNamespace $namespace)
    {
        $this->prefix = 'http://ogp.me/ns# ' . $namespace->namespace . ": http://ogp.me/ns/$namespace->namespace#";
        $this->offsetSet('type', $namespace->namespace);

        foreach ($namespace->getArrayCopy() as $property => $element) {
            parent::offsetSet($property, $element);
        }

        return $this;
    }

    public function setAppId($appID)
    {
        $element = new Element('meta');

        $element->attributes[ 'name' ] = 'fb:app_id';
        $element->attributes[ 'content' ] = $appID;

        parent::offsetSet('fb:app_id', $element);

        return $this;
    }

    public function setUrl($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $this->setObject('url', $url);
        }

        return $this;
    }

    public function setObject($property, $content)
    {
        $property = 'og:' . $property;
        $element = new Element('meta');

        $element->attributes[ 'name' ] = $property;
        $element->attributes[ 'content' ] = (is_array($content) ? implode(', ', $content) : trim($content));

        parent::offsetSet($property, $element);

        return $this;
    }

    public function setSiteName($siteName)
    {
        $this->setObject('site_name', $siteName);

        return $this;
    }

    public function setTitle($title)
    {
        $this->setObject('title', $title);

        return $this;
    }

    public function setDescription($description)
    {
        $this->setObject('description', $description);

        return $this;
    }

    public function setType($type)
    {
        $this->setObject('type', $type);

        return $this;
    }

    public function setImage($image)
    {
        if (getimagesize($image)) {
            if (strpos($image, 'http') === false) {
                loader()->loadHelper('url');
                $image = images_url($image);
            }

            $this->setObject('image', $image);
        }

        return $this;
    }

    public function setDeterminer($determiner)
    {
        $this->setObject('determiner', $determiner);

        return $this;
    }

    public function setEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setObject('email', $email);
        }

        return $this;
    }

    public function setLocale($lang, $territory = null)
    {
        $lang = strtolower($lang);

        $this->setObject(
            'locale',
            (isset($territory) ? $lang . '_' . strtoupper($territory) : $lang)
        );

        return $this;
    }

    public function setLocaleAlternate($lang, $territory = null)
    {
        $lang = strtolower($lang);

        $this->setObject(
            'locale:alternate',
            (isset($territory) ? $lang . '_' . strtoupper($territory) : $lang)
        );

        return $this;
    }

    public function setStreetAddress($streetAddress)
    {
        $this->setObject('street_address', $streetAddress);

        return $this;
    }

    public function setPostalCode($postalCode)
    {
        $this->setObject('postal_code', $postalCode);

        return $this;
    }

    public function setCountryName($countryName)
    {
        $this->setObject('country_name', $countryName);

        return $this;
    }

    public function setLocacity($locacity)
    {
        $this->setObject('locacity', $locacity);

        return $this;
    }

    public function setRegion($region)
    {
        $this->setObject('region', $region);

        return $this;
    }

    public function setMap($latitude, $longitude)
    {
        $this->setObject('latitude', $latitude);
        $this->setObject('longitude', $longitude);

        return $this;
    }

    public function setPhoneNumber($phoneNumber)
    {
        $this->setObject('phone_number', $phoneNumber);

        return $this;
    }

    public function setFaxNumber($faxNumber)
    {
        $this->setObject('fax_number', $faxNumber);

        return $this;
    }

    public function __toString()
    {
        $output = '';

        if ($this->count()) {
            foreach ($this->storage as $offset => $tag) {
                if ($tag instanceof Element) {
                    $output .= $tag->render();
                }
            }
        }

        return $output;
    }
}