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

namespace O2System\Framework\Libraries;

// ------------------------------------------------------------------------

use O2System\Spl\Traits\Collectors\ErrorCollectorTrait;

/**
 * Class Email
 *
 * @package O2System\Framework\Libraries
 */
class Email
{
    use ErrorCollectorTrait;

    /**
     * Email::$from
     *
     * Email from.
     *
     * @var string
     */
    protected $from;

    /**
     * Email::$replyTo
     *
     * Email reply-to.
     *
     * @var string
     */
    protected $replyTo;

    /**
     * Email::$to
     *
     * Email to Receiver, or receivers of the mail.
     *
     * @var array
     */
    protected $to = [];

    /**
     * Email::$cc
     *
     * Email copy carbon of Receiver, or receivers of the mail.
     *
     * @var array
     */
    protected $cc = [];

    /**
     * Email::$bcc
     *
     * Email blank copy carbon of Receiver, or receivers of the mail.
     *
     * @var array
     */
    protected $bcc = [];

    /**
     * Email::$subject
     *
     * Subject of the email to be sent.
     *
     * @var string
     */
    protected $subject;

    /**
     * Email::$message
     *
     * Message to be sent.
     *
     * @var string
     */
    protected $message;

    /**
     * Email::$headers
     *
     * Headers of the message to be sent.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Email::$attachments
     *
     * Email attachments.
     *
     * @var array
     */
    protected $attachments = [];

    /**
     * Email::$userAgent
     *
     * Used as the User-Agent and X-Mailer headers' value.
     */
    protected $userAgent = 'O2System';

    /**
     * Email::$charset
     *
     * Character set (default: utf-8)
     *
     * @var string
     */
    protected $charset = 'utf-8';

    /**
     * Email::$contentType
     *
     * Email body content type
     *
     * @var array
     */
    protected $contentType = 'text';

    /**
     * Email::$priority
     *
     * Email priority
     *
     * @var string
     */
    protected $priority;

    // ------------------------------------------------------------------------

    /**
     * Email::setCharset
     *
     * Sets mail charset.
     *
     * @param string $charset
     *
     * @return static
     */
    public function setCharset( $charset )
    {
        $this->charset = $charset;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Email::setFrom
     *
     * Sets mail from.
     *
     * @param string $email
     * @param string $name
     *
     * @return static
     */
    public function setFrom( $email, $name = null )
    {
        $this->setAddress( $email, $name, 'from' );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Email::setReplyTo
     *
     * Sets reply to mail address.
     *
     * @param string $email
     * @param string $name
     *
     * @return static
     */
    public function setReplyTo( $email, $name = null )
    {
        $this->setAddress( $email, $name, 'replyTo' );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Email::setFrom
     *
     * Sets mail from.
     *
     * @param string $email
     * @param string $name
     * @param string $object from | replyTo
     *
     * @return void
     */
    public function setAddress( $email, $name = null, $object )
    {
        if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
            if ( is_null( $name ) ) {
                $this->{$object} = trim( $email );
            } else {
                $this->{$object} = trim( $email ) . ' <' . trim( $email ) . '>';
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Email::setSubject
     *
     * Sets mail subject.
     *
     * @param string $subject
     *
     * @return static
     */
    public function setSubject( $subject )
    {
        $this->subject = trim( $subject );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Email::setMessage
     *
     * Sets mail message.
     *
     * @param string $message
     *
     * @return static
     */
    public function setMessage( $message )
    {
        $this->message = trim( $message );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Email::setPriority
     *
     * Sets mail priority
     *
     * @param int $priority
     */
    public function setPriority( $priority )
    {
        $priorities = [
            1 => '1 (Highest)',
            2 => '2 (High)',
            3 => '3 (Normal)',
            4 => '4 (Low)',
            5 => '5 (Lowest)',
        ];

        if ( array_key_exists( $priority, $priorities ) ) {
            $this->priority = $priorities[ $priority ];
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Email::addHeader
     *
     * Add additional mail header.
     *
     * @param string $name
     * @param string $value
     *
     * @return static
     */
    public function addHeader( $name, $value )
    {
        $this->headers[ $name ] = $value;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Email::addTo
     *
     * Add to mail.
     *
     * @param string $email
     * @param string $name
     */
    public function addTo( $email, $name = null )
    {
        $this->addAddress( $email, $name, 'to' );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Email::addCc
     *
     * Add to copy carbon mail.
     *
     * @param string $email
     * @param string $name
     */
    public function addCc( $email, $name = null )
    {
        $this->addAddress( $email, $name, 'cc' );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Email::addBcc
     *
     * Add to blank copy carbon mail.
     *
     * @param string $email
     * @param string $name
     */
    public function addBcc( $email, $name = null )
    {
        $this->addAddress( $email, $name, 'bcc' );

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Email::addAddress
     *
     * Add mail address to object.
     *
     * @param string $email
     * @param string $name
     * @param string $object
     *
     * @return void
     */
    protected function addAddress( $email, $name = null, $object )
    {
        if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
            if ( is_null( $name ) ) {
                $this->{$object}[] = trim( $email );
            } else {
                $this->{$object}[] = trim( $email ) . ' <' . trim( $email ) . '>';
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Email::addAttachment
     *
     * Add mail attachment.
     *
     * @param mixed $attachment
     */
    public function addAttachment( $attachment )
    {
        if ( file_exists( $attachment ) ) {
            $this->attachments[] = $attachment;
        }
    }

    public static function prepareAttachment( $path )
    {
        if ( file_exists( $path ) ) {
            $finfo = finfo_open( FILEINFO_MIME_TYPE );
            $ftype = finfo_file( $finfo, $path );
            $file = fopen( $path, "r" );
            $attachment = fread( $file, filesize( $path ) );
            $attachment = chunk_split( base64_encode( $attachment ) );
            fclose( $file );

            $msg = 'Content-Type: \'' . $ftype . '\'; name="' . basename( $path ) . '"' . PHP_EOL;
            $msg .= "Content-Transfer-Encoding: base64" . PHP_EOL;
            $msg .= 'Content-ID: <' . basename( $path ) . '>' . PHP_EOL;
//            $msg .= 'X-Attachment-Id: ebf7a33f5a2ffca7_0.1' . PHP_EOL;
            $msg .= PHP_EOL . $attachment . PHP_EOL . PHP_EOL;

            return $msg;
        } else {
            return false;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Email::send
     *
     * Email sending trigger.
     *
     * @return bool
     */
    public function send()
    {
        $boundary = md5( rand() );
        $boundaryContent = md5( rand() );

        // Headers from
        $headers = 'From: ' . $this->from . PHP_EOL;
        $headers .= 'Reply-To: ' . $this->replyTo . PHP_EOL;
        $headers .= 'Mime-Version: 1.0' . PHP_EOL;
        $headers .= 'Content-Type: multipart/related;boundary=' . $boundary . PHP_EOL;

        // Headers cc and bcc
        if ( count( $this->cc ) ) {
            $headers .= 'Cc: ' . implode( ';', $this->cc ) . PHP_EOL;
        }
        if ( count( $this->bcc ) ) {
            $headers .= 'Bcc: ' . implode( ';', $this->bcc ) . PHP_EOL;
        }
        $headers .= PHP_EOL;

        // Message Body
        $body = PHP_EOL . '--' . $boundary . PHP_EOL;
        $body .= "Content-Type: multipart/alternative;" . PHP_EOL;
        $body .= " boundary=\"$boundaryContent\"" . PHP_EOL;

//Body Mode text
        $body .= PHP_EOL . "--" . $boundaryContent . PHP_EOL;
        $body .= 'Content-Type: text/plain; charset=ISO-8859-1' . PHP_EOL;
        $body .= strip_tags( $this->message ) . PHP_EOL;

//Body Mode Html
        $body .= PHP_EOL . "--" . $boundaryContent . PHP_EOL;
        $body .= 'Content-Type: text/html; charset=ISO-8859-1' . PHP_EOL;
        $body .= 'Content-Transfer-Encoding: quoted-printable' . PHP_EOL;
        if ( $_headers ) {
            $body .= PHP_EOL . '<img src=3D"cid:template-H.PNG" />' . PHP_EOL;
        }
        //equal sign are email special characters. =3D is the = sign
        $body .= PHP_EOL . '<div>' . nl2br( str_replace( "=", "=3D", $content ) ) . '</div>' . PHP_EOL;
        if ( $_headers ) {
            $body .= PHP_EOL . '<img src=3D"cid:template-F.PNG" />' . PHP_EOL;
        }
        $body .= PHP_EOL . '--' . $boundaryContent . '--' . PHP_EOL;

//if attachement
        if ( $path != '' && file_exists( $path ) ) {
            $conAttached = self::prepareAttachment( $path );
            if ( $conAttached !== false ) {
                $body .= PHP_EOL . '--' . $boundary . PHP_EOL;
                $body .= $conAttached;
            }
        }

//other attachement : here used on HTML body for picture headers/footers
        if ( $_headers ) {
            $imgHead = dirname( __FILE__ ) . '/../../../../modules/notification/ressources/img/template-H.PNG';
            $conAttached = self::prepareAttachment( $imgHead );
            if ( $conAttached !== false ) {
                $body .= PHP_EOL . '--' . $boundary . PHP_EOL;
                $body .= $conAttached;
            }
            $imgFoot = dirname( __FILE__ ) . '/../../../../modules/notification/ressources/img/template-F.PNG';
            $conAttached = self::prepareAttachment( $imgFoot );
            if ( $conAttached !== false ) {
                $body .= PHP_EOL . '--' . $boundary . PHP_EOL;
                $body .= $conAttached;
            }
        }

        // Fin
        $body .= PHP_EOL . '--' . $boundary . '--' . PHP_EOL;

// Function mail()
        mail( implode( '; ', $this->to ), $this->subject, $body, $headers );
    }

    protected function validateEmail( $email )
    {
        if ( function_exists( 'idn_to_ascii' ) && $atpos = strpos( $email, '@' ) ) {
            $email = self::substr( $email, 0, ++$atpos ) . idn_to_ascii( self::substr( $email, $atpos ) );
        }

        return (bool)filter_var( $email, FILTER_VALIDATE_EMAIL );
    }

    /**
     * Byte-safe substr()
     *
     * @param    string $str
     * @param    int    $start
     * @param    int    $length
     *
     * @return    string
     */
    protected static function substr( $str, $start, $length = null )
    {
        if ( self::$func_overload ) {
            return mb_substr( $str, $start, $length, '8bit' );
        }

        return isset( $length )
            ? substr( $str, $start, $length )
            : substr( $str, $start );
    }
}