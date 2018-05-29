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
 * Class Book
 *
 * @package O2System\Framework\Http\Presenter\Meta\Opengraph
 */
class Book extends AbstractNamespace
{
    use AuthorTrait;

    public $namespace = 'book';

    // ------------------------------------------------------------------------

    /**
     * Book::setReleaseDate
     *
     * @param $datetime
     *
     * @return \O2System\Framework\Http\Presenter\Meta\Opengraph\Book
     */
    public function setReleaseDate($datetime)
    {
        return $this->setObject('release_date', $datetime);
    }

    // ------------------------------------------------------------------------

    /**
     * Book::setIsbn
     *
     * @param $isbn
     *
     * @return \O2System\Framework\Http\Presenter\Meta\Opengraph\Book
     */
    public function setIsbn($isbn)
    {
        return $this->setObject('isbn', $isbn);
    }
}