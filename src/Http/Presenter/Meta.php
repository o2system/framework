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

namespace O2System\Framework\Http\Presenter;

// ------------------------------------------------------------------------

use O2System\Html\Element;
use O2System\Spl\Patterns\Structural\Repository\AbstractRepository;

/**
 * Class Meta
 *
 * @package O2System\Framework\Http\Presenter
 */
class Meta extends AbstractRepository
{
    /**
     * Meta::$title
     *
     * @var \O2System\Framework\Http\Presenter\Meta\Title
     */
    public $title;

    /**
     * Meta::$opengraph
     *
     * @var \O2System\Framework\Http\Presenter\Meta\Opengraph
     */
    public $opengraph;

    // ------------------------------------------------------------------------

    /**
     * Meta::__construct
     */
    public function __construct()
    {
        $this->title = new Meta\Title();

        if (false !== ($config = config('view')->presenter)) {
            if (false !== ($config->offsetGet('socialGraph'))) {
                $this->opengraph = new Meta\Opengraph();
                $this->opengraph->setTitle($this->title);
                $this->opengraph->setLocale(language()->getDefaultLocale(), language()->getDefaultIdeom());
                $this->opengraph->setUrl(current_url());
            }
        }

        $this->offsetSet('language', language()->getDefault());
        $this->offsetSet('generator', FRAMEWORK_NAME . ' v' . FRAMEWORK_VERSION);
        $this->offsetSet('url', current_url());
    }

    // ------------------------------------------------------------------------

    /**
     * Meta::store
     *
     * @param string $offset
     * @param mixed  $value
     */
    public function store($offset, $value)
    {
        $element = new Element('meta');

        if ($offset === 'http-equiv') {
            $element->attributes[ 'http-equiv' ] = $value[ 'property' ];
            $element->attributes[ 'content' ] = $value[ 'content' ];
            parent::store(camelcase('http_equiv_' . $value[ 'property' ]), $element);
        } else {
            $element->attributes[ 'name' ] = $offset;

            if (is_array($value)) {
                if (is_numeric(key($value))) {
                    $element->attributes[ 'content' ] = implode(', ', $value);
                } else {
                    $newValue = [];
                    foreach ($value as $key => $val) {
                        $newValue[] = $key . '=' . $val;
                    }
                    $element->attributes[ 'content' ] = implode(', ', $newValue);
                }
            } else {
                $element->attributes[ 'content' ] = $value;
            }

            if (in_array($offset, ['description']) and $this->opengraph instanceof Meta\Opengraph) {
                $this->opengraph->setObject($element->attributes[ 'name' ], $element->attributes[ 'content' ]);
            }

            parent::store(camelcase($offset), $element);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Meta::__toString
     *
     * Converts this meta object into a string.
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