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

namespace O2System\Framework\Http\Presenter\Meta\Opengraph;

// ------------------------------------------------------------------------

use O2System\Framework\Http\Presenter\Meta\Opengraph\Abstracts\AbstractNamespace;
use O2System\Framework\Http\Presenter\Meta\Opengraph\Traits\AuthorTrait;

/**
 * Class Article
 *
 * @package O2System\Framework\Http\Presenter\Meta\Opengraph
 */
class Article extends AbstractNamespace
{
    use AuthorTrait;

    public $namespace = 'article';

    /**
     * Article::setPublishedTime
     *
     * @param $datetime
     *
     * @return \O2System\Framework\Http\Presenter\Meta\Opengraph\Article
     */
    public function setPublishedTime($datetime)
    {
        return $this->setObject('published_time', $datetime);
    }

    // ------------------------------------------------------------------------

    /**
     * Article::setModifiedTime
     *
     * @param $datetime
     *
     * @return \O2System\Framework\Http\Presenter\Meta\Opengraph\Article
     */
    public function setModifiedTime($datetime)
    {
        return $this->setObject('modified_time', $datetime);
    }

    // ------------------------------------------------------------------------

    /**
     * Article::setExpirationTime
     *
     * @param $datetime
     *
     * @return \O2System\Framework\Http\Presenter\Meta\Opengraph\Article
     */
    public function setExpirationTime($datetime)
    {
        return $this->setObject('expiration_time', $datetime);
    }

    // ------------------------------------------------------------------------

    /**
     * Article::section
     *
     * @param $section
     *
     * @return \O2System\Framework\Http\Presenter\Meta\Opengraph\Article
     */
    public function setSection($section)
    {
        return $this->setObject('section', $section);
    }
}