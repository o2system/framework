<?php
/**
 * This file is part of the O2System Framework package.
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
use O2System\Spl\Patterns\Structural\Repository\AbstractRepository;

/**
 * Class Opengraph
 *
 * @package O2System\Framework\Http\Presenter\Meta
 */
class Opengraph extends AbstractRepository
{
    /**
     * Opengraph::$prefix
     *
     * @var string
     */
    public $prefix;

    // ------------------------------------------------------------------------

    /**
     * Opengraph::__construct
     */
    public function __construct()
    {
        $this->prefix = 'http://ogp.me/ns#';
    }

    // ------------------------------------------------------------------------

    /**
     * Opengraph::setNamespace
     *
     * @param \O2System\Framework\Http\Presenter\Meta\Opengraph\Abstracts\AbstractNamespace $namespace
     *
     * @return static
     */
    public function setNamespace(Opengraph\Abstracts\AbstractNamespace $namespace)
    {
        $this->prefix = 'http://ogp.me/ns# ' . $namespace->namespace . ": http://ogp.me/ns/$namespace->namespace#";
        $this->offsetSet('type', $namespace->namespace);

        foreach ($namespace->getArrayCopy() as $property => $element) {
            parent::offsetSet($property, $element);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Opengraph::setAppId
     *
     * @param string $appId
     *
     * @return static
     */
    public function setAppId($appId)
    {
        $element = new Element('meta');

        $element->attributes[ 'name' ] = 'fb:app_id';
        $element->attributes[ 'content' ] = $appId;

        parent::offsetSet('fb:app_id', $element);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Opengraph::setUrl
     *
     * @param string $url
     *
     * @return static
     */
    public function setUrl($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $this->setObject('url', $url);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Opengraph::setObject
     *
     * @param string $property
     * @param string $content
     *
     * @return static
     */
    public function setObject($property, $content)
    {
        $property = 'og:' . $property;
        $element = new Element('meta');

        $element->attributes[ 'name' ] = $property;
        $element->attributes[ 'content' ] = (is_array($content) ? implode(', ', $content) : trim($content));

        parent::offsetSet($property, $element);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Opengraph::setSiteName
     *
     * @param string $siteName
     *
     * @return static
     */
    public function setSiteName($siteName)
    {
        $this->setObject('site_name', $siteName);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Opengraph::setTitle
     *
     * @param string $title
     *
     * @return static
     */
    public function setTitle($title)
    {
        $this->setObject('title', $title);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Opengraph::setDescription
     *
     * @param string $description
     *
     * @return static
     */
    public function setDescription($description)
    {
        $this->setObject('description', $description);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Opengraph::setType
     *
     * @param string $type
     *
     * @return static
     */
    public function setType($type)
    {
        $this->setObject('type', $type);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Opengraph::setImage
     *
     * @param $image
     *
     * @return static
     */
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

    // ------------------------------------------------------------------------

    /**
     * Opengraph::setDeterminer
     *
     * @param string $determiner
     *
     * @return static
     */
    public function setDeterminer($determiner)
    {
        $this->setObject('determiner', $determiner);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Opengraph::setEmail
     *
     * @param string $email
     *
     * @return static
     */
    public function setEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setObject('email', $email);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Opengraph::setLocale
     *
     * @param string      $lang
     * @param string|null $territory
     *
     * @return static
     */
    public function setLocale($lang, $territory = null)
    {
        $lang = strtolower($lang);

        $this->setObject(
            'locale',
            (isset($territory) ? $lang . '_' . strtoupper($territory) : $lang)
        );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Opengraph::setLocaleAlternate
     *
     * @param string      $lang
     * @param string|null $territory
     *
     * @return static
     */
    public function setLocaleAlternate($lang, $territory = null)
    {
        $lang = strtolower($lang);

        $this->setObject(
            'locale:alternate',
            (isset($territory) ? $lang . '_' . strtoupper($territory) : $lang)
        );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Opengraph::setStreetAddress
     *
     * @param string $streetAddress
     *
     * @return static
     */
    public function setStreetAddress($streetAddress)
    {
        $this->setObject('street_address', $streetAddress);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Opengraph::setPostalCode
     *
     * @param string $postalCode
     *
     * @return static
     */
    public function setPostalCode($postalCode)
    {
        $this->setObject('postal_code', $postalCode);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Opengraph::setCountryName
     *
     * @param string $countryName
     *
     * @return static
     */
    public function setCountryName($countryName)
    {
        $this->setObject('country_name', $countryName);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Opengraph::setLocacity
     *
     * @param string $locacity
     *
     * @return static
     */
    public function setLocacity($locacity)
    {
        $this->setObject('locacity', $locacity);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Opengraph::setRegion
     *
     * @param string $region
     *
     * @return static
     */
    public function setRegion($region)
    {
        $this->setObject('region', $region);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Opengraph::setMap
     *
     * @param float $latitude
     * @param float $longitude
     *
     * @return static
     */
    public function setMap($latitude, $longitude)
    {
        $this->setObject('latitude', $latitude);
        $this->setObject('longitude', $longitude);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Opengraph::setPhoneNumber
     *
     * @param int|string $phoneNumber
     *
     * @return static
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->setObject('phone_number', $phoneNumber);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Opengraph::setFaxNumber
     *
     * @param int|string $faxNumber
     *
     * @return static
     */
    public function setFaxNumber($faxNumber)
    {
        $this->setObject('fax_number', $faxNumber);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Opengraph::__toString
     *
     * @return string
     */
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