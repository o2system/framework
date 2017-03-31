<?php
/**
 * Created by PhpStorm.
 * User: steevenz
 * Date: 3/17/17
 * Time: 4:51 PM
 */

namespace O2System\Framework\Libraries;


use O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException;

class Email
{
    protected $config = [];

    /**
     * PHPMailer Handler
     *
     * @type
     */
    protected $phpMailer;

    protected $errors = [];

    public function __construct( $config = [] )
    {
        if ( ! class_exists( 'PHPMailer' ) ) {
            throw new BadDependencyCallException('E_EMAIL_PHPMAILER');
        }

        if ( empty( $config ) ) {
            $config = O2System()->config->load( 'email', true );
        }

        $this->config[ 'charset' ] = O2System()->config[ 'charset' ];

        if ( isset( $config[ 'from' ] ) ) {
            if ( is_array( $config[ 'from' ] ) ) {
                if ( isset( $config[ 'from' ][ 'email' ] ) ) {
                    $this->config[ 'from' ] = $config[ 'from' ][ 'email' ];
                }

                if ( isset( $config[ 'from' ][ 'name' ] ) ) {
                    $this->config[ 'from_name' ] = $config[ 'from' ][ 'name' ];
                }

                if ( isset( $config[ 'from' ][ 'return_path' ] ) ) {
                    $this->config[ 'return_path' ] = $config[ 'from' ][ 'return_path' ];
                }
            } else {
                $this->config[ 'from' ] = $this->config[ 'from_name' ] = $config[ 'from' ];
            }

            unset( $config[ 'from' ] );
        } else {
            $this->config[ 'from' ] = 'no-reply@' . str_replace( 'www.', '', O2System()->request->origin );
            $this->config[ 'from_name' ] = 'no-reply';
        }

        if ( isset( $config[ 'protocol' ] ) ) {
            if ( is_string( $config[ 'protocol' ] ) ) {
                $protocol = $config[ 'protocol' ];
                $settings = [];
            } else {
                $protocol = key( $config[ 'protocol' ] );
                $settings = $config[ 'protocol' ][ $protocol ];
            }

            $this->config[ 'protocol' ] = [ $protocol, $settings ];

            unset( $config[ 'protocol' ] );
        }

        $this->phpMailer = new \PHPMailer;

        if ( isset( $config ) AND is_array( $config ) ) {
            $this->config = array_merge_recursive( $this->config, $config );
        }

        foreach ( $this->config as $key => $value ) {
            if ( is_string( $value ) ) {
                $value = [ $value ];
            }

            if ( method_exists( $this, strtolower( $key ) ) ) {
                call_user_func_array( [ $this, strtolower( $key ) ], $value );
            } elseif ( method_exists( $this, 'set_' . strtolower( $key ) ) ) {
                call_user_func_array( [ $this, 'set_' . strtolower( $key ) ], $value );
            }
        }
    }

    public function setCharset( $charset )
    {
        $this->phpMailer->CharSet = $charset;

        return $this;
    }

    public function setProtocol()
    {
        $args = func_get_args();

        if ( ! in_array( $args[ 0 ], [ 'mail', 'sendmail', 'smtp' ] ) ) {
            throw new \BadMethodCallException( 'Email: Invalid Email Protocol' );
        } else {
            $this->phpMailer->Mailer = $args[ 0 ];
        }

        if ( $args[ 0 ] === 'sendmail' ) {
            if ( ! empty( $args[ 1 ] ) ) {
                $this->phpMailer->Sendmail( $args[ 1 ] );
            }
        } elseif ( $args[ 0 ] === 'smtp' ) {
            $this->phpMailer->isSMTP();

            if ( ! empty( $args[ 1 ] ) ) {
                if ( isset( $args[ 1 ][ 'username' ] ) AND isset( $args[ 1 ][ 'password' ] ) ) {
                    $this->phpMailer->Username = $args[ 1 ][ 'username' ];
                    $this->phpMailer->Password = $args[ 1 ][ 'password' ];
                    $this->phpMailer->SMTPAuth = true;
                }

                if ( isset( $args[ 1 ][ 'port' ] ) ) {
                    $this->phpMailer->Port = $args[ 1 ][ 'port' ];
                }

                if ( isset( $args[ 1 ][ 'host' ] ) ) {
                    $this->phpMailer->Host = $args[ 1 ][ 'host' ];
                    $this->setHost( $args[ 1 ][ 'host' ] );
                }
            }
        }

        return $this;
    }

    public function setHost( $host )
    {
        $this->phpMailer->Host = $host;

        return $this;
    }

    public function subject( $subject )
    {
        $this->phpMailer->Subject = $subject;

        return $this;
    }

    public function message( $body, $vars = [] )
    {
        if ( O2System()->__isset( 'view' ) ) {
            $body = O2System()->view->load( $body, (array)$vars, true );
        } elseif ( is_file( $body ) ) {
            $body = file_get_contents( $body );
            $body = htmlspecialchars_decode( $body );

            if ( count( $vars ) > 0 ) {
                extract( $vars );

                /*
                 * Buffer the output
                 *
                 * We buffer the output for two reasons:
                 * 1. Speed. You get a significant speed boost.
                 * 2. So that the final rendered template can be post-processed by
                 *	the output class. Why do we need post processing? For one thing,
                 *	in order to show the elapsed page load time. Unless we can
                 *	intercept the content right before it's sent to the browser and
                 *	then stop the timer it won't be accurate.
                 */
                ob_start();

                // If the PHP installation does not support short tags we'll
                // do a little string replacement, changing the short tags
                // to standard PHP echo statements.
                if ( ! ini_get( 'short_open_tag' ) AND function_usable( 'eval' ) ) {
                    echo eval( '?>' . preg_replace( '/;*\s*\?>/', '; ?>',
                            str_replace( '<?=', '<?php echo ', $body ) ) );
                } else {
                    echo eval( '?>' . preg_replace( '/;*\s*\?>/', '; ?>', $body ) );
                }

                $body = ob_get_contents();
                @ob_end_clean();
            }
        }

        $this->setContentType( 'html' );

        $this->phpMailer->Body = $body;
        $this->phpMailer->AltBody = strip_tags( $body );

        return $this;
    }

    public function setContentType( $type )
    {
        if ( ! in_array( $type, [ 'html', 'plain', 'text' ] ) ) {
            throw new \BadMethodCallException( 'Email: Invalid Email Content Type' );
        }

        if ( $type === 'html' ) {
            $this->phpMailer->isHTML( true );
        }

        return $this;
    }

    public function altMessage( $message, $vars = [] )
    {
        if ( O2System()->__isset( 'view' ) ) {
            $message = O2System()->view->load( $message, $vars, true );
        } elseif ( is_file( $message ) ) {
            $message = file_get_contents( $message );
            $message = htmlspecialchars_decode( $message );

            if ( count( $vars ) > 0 ) {
                extract( $vars );

                /*
                 * Buffer the output
                 *
                 * We buffer the output for two reasons:
                 * 1. Speed. You get a significant speed boost.
                 * 2. So that the final rendered template can be post-processed by
                 *	the output class. Why do we need post processing? For one thing,
                 *	in order to show the elapsed page load time. Unless we can
                 *	intercept the content right before it's sent to the browser and
                 *	then stop the timer it won't be accurate.
                 */
                ob_start();

                // If the PHP installation does not support short tags we'll
                // do a little string replacement, changing the short tags
                // to standard PHP echo statements.
                if ( ! ini_get( 'short_open_tag' ) AND function_usable( 'eval' ) ) {
                    echo eval( '?>' . preg_replace( '/;*\s*\?>/', '; ?>',
                            str_replace( '<?=', '<?php echo ', $message ) ) );
                } else {
                    echo eval( '?>' . preg_replace( '/;*\s*\?>/', '; ?>', $message ) );
                }

                $message = ob_get_contents();
                @ob_end_clean();
            }
        }

        $this->phpMailer->AltBody = strip_tags( $message );

        return $this;
    }

    public function from( $address, $name = null, $return_path = null )
    {
        $name = isset( $name ) ? $name : @$this->config[ 'from_name' ];

        $this->phpMailer->setFrom( $address, $name );

        if ( isset( $return_path ) ) {
            $this->returnPath( $return_path );
        }

        return $this;
    }

    public function returnPath( $return_path )
    {
        $this->phpMailer->ReturnPath = $return_path;

        return $this;
    }

    public function fromName( $name )
    {
        $this->phpMailer->FromName = $name;

        return $this;
    }

    public function replyTo( $address, $name )
    {
        $this->phpMailer->addReplyTo( $address, $name );

        return $this;
    }

    public function to( $address, $name = null )
    {
        if ( is_array( $address ) ) {
            foreach ( $address as $email => $name ) {
                if ( is_numeric( $email ) ) {
                    $this->phpMailer->addAddress( $name );
                } else {
                    $this->phpMailer->addAddress( $email, $name );
                }
            }
        } elseif ( is_string( $address ) ) {
            if ( strpos( $address, ',' ) ) {
                $address = explode( ',', $address );
                $address = array_map( 'trim', $address );

                foreach ( $address as $email ) {
                    $this->phpMailer->addAddress( $email );
                }
            } else {
                $this->phpMailer->addAddress( $address, $name );
            }
        }

        return $this;
    }

    public function cc( $address, $name = null )
    {
        if ( is_array( $address ) ) {
            foreach ( $address as $email => $name ) {
                if ( is_numeric( $email ) ) {
                    $this->phpMailer->addCC( $name );
                } else {
                    $this->phpMailer->addCC( $email, $name );
                }
            }
        } elseif ( is_string( $address ) ) {
            if ( strpos( $address, ',' ) ) {
                $address = explode( ',', $address );
                $address = array_map( 'trim', $address );

                foreach ( $address as $email ) {
                    $this->phpMailer->addCC( $email );
                }
            } else {
                $this->phpMailer->addCC( $address, $name );
            }
        }

        return $this;
    }

    public function bcc( $address, $name )
    {
        if ( is_array( $address ) ) {
            foreach ( $address as $email => $name ) {
                if ( is_numeric( $email ) ) {
                    $this->phpMailer->addBCC( $name );
                } else {
                    $this->phpMailer->addBCC( $email, $name );
                }
            }
        } elseif ( is_string( $address ) ) {
            if ( strpos( $address, ',' ) ) {
                $address = explode( ',', $address );
                $address = array_map( 'trim', $address );

                foreach ( $address as $email ) {
                    $this->phpMailer->addBCC( $email );
                }
            } else {
                $this->phpMailer->addBCC( $address, $name );
            }
        }

        return $this;
    }

    public function attach( $attachment, $name = null )
    {
        if ( is_array( $attachment ) ) {
            foreach ( $attachment as $file => $name ) {
                if ( is_numeric( $file ) ) {
                    $this->attach( $file );
                } else {
                    $this->attach( $file, $name );
                }
            }
        } else {
            if ( is_file( $attachment ) ) {
                $this->phpMailer->addAttachment( $attachment, $name );
            }
        }

        return $this;
    }

    public function getBody()
    {
        return $this->phpMailer->Body;
    }

    public function getAltBody()
    {
        return $this->phpMailer->AltBody;
    }

    public function send()
    {
        try {
            if ( $this->phpMailer->send() ) {
                return true;
            } else {
                $this->errors[] = $this->phpMailer->ErrorInfo;
            }
        } catch ( \phpmailerException $e ) {
            $this->errors[] = $e->getMessage();
        } catch ( \Exception $e ) {
            $this->errors[] = $e->getMessage();
        }

        return false;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}