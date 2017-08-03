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

namespace O2System\Framework\Libraries\Ui\Traits\Collectors;

// ------------------------------------------------------------------------
use O2System\Html\Element;
use O2System\Spl\Iterators\ArrayIterator;

/**
 * Class ParagraphsCollectorTrait
 *
 * @package O2System\Libraries\Ui\Traits
 */
trait ParagraphsCollectorTrait
{
   public $paragraphs;

   public function hasParagraphs()
   {
       if( $this->paragraphs instanceof ArrayIterator ) {
           if( $this->paragraphs->count() ) {
               return true;
           }
       }

       return false;
   }

   public function createParagraph( $text )
   {
       $paragraph = new Element( 'p', 'paragraph' );
       $paragraph->textContent->push( $text );

       if( ! $this->paragraphs instanceof ArrayIterator ) {
           $this->paragraphs = new ArrayIterator();
       }

       $this->paragraphs->push( $paragraph );

       return $this->paragraphs->last();
   }

   public function addParagraph( Element $paragraph )
   {
       $paragraph->tagName = 'p';

       if( ! $this->paragraphs instanceof ArrayIterator ) {
           $this->paragraphs = new ArrayIterator();
       }

       $this->paragraphs->push( $paragraph );

       return $this->paragraphs->last();
   }
}