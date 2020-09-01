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

namespace O2System\Framework\Http\Controllers\System;

// ------------------------------------------------------------------------

use O2System\Framework\Http\Controllers\Restful;
use O2System\Security\Authentication\JsonWebToken;

// ------------------------------------------------------------------------
/**
 * Class Users
 * @package O2System\Framework\Http\Controllers\System
 */
class Users extends Restful
{
    /**
     * Users::$model
     *
     * @var string
     */
    public $model = '\O2System\Framework\Models\Sql\System\Users';



    // ------------------------------------------------------------------------

    /**
     * Users::$searchableColumns
     *
     * @var array
     */
    public $searchableColumns = [
        'email',
        'username',
        'msisdn'
    ];

    // ------------------------------------------------------------------------
    /**
     * Users::authenticate
     *
     * @throws \O2System\Spl\Exceptions\Logic\DomainException
     * @throws \Exception
     */
    public function authenticate()
    {
        if ($post = input()->post()) {
            $post->validation([
                'username' => 'required',
                'password' => 'required',
            ], [
                'username' => [
                    'required' => 'Username cannot be empty!',
                ],
                'password' => [
                    'required' => 'Password cannot be empty!',
                ],
            ]);

            if ($post->validate()) {
                if (services()->has('accessControl')) {
                    if (services('accessControl')->authenticate($post->username, $post->password)) {
                        if (services('accessControl')->loggedIn()) {
                            if($post->redirect and ! is_ajax()) {
                                redirect_url($post->redirect);
                            }

                            $this->sendPayload([
                                'ssid' => session()->ssid,
                                'jwt' => session()->jwt,
                                'expires' => gmdate('D, d M Y H:i:s e', strtotime(session()->expires))
                            ]);
                        } else {
                            $this->sendPayload([
                                'success' => false,
                                'message' => 'Login failed, please try again in a few minutes!',
                            ]);
                        }
                    } else {
                        $this->sendPayload([
                            'success' => false,
                            'message' => 'Username or password is not found!',
                        ]);
                    }
                } else {
                    $this->sendError(503, 'Service access control is not exists!');
                }
            } else {
                $this->sendError(400, 'Username and password cannot be empty!');
            }
        } else {
            $this->sendError(400);
        }
    }
    // ------------------------------------------------------------------------
}
