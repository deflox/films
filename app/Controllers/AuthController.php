<?php

/**
 * Controller for handling all authentication
 * requests. 
 * 
 * @author Leo Rudin
 */

namespace App\Controllers;

use App\Accessor;

class AuthController extends Accessor
{
    /**
     * Post request for login. 
     * 
     * @param  $req
     * @param  $res
     * @return string
     */
    public function postLogin($req, $res)
    {
        $json = [
            'error' => false,
            'error_messages' => []
        ];

        if (empty($req->getParam('username')) || empty($req->getParam('password'))) {
            $json['error'] = true;
            array_push($json['error_messages'], "Please fill out all required fields.");
        } else if (!$this->auth->attempt($req->getParam('username'), $req->getParam('password'), $req->getAttribute('ip_address'))) {
            $json['error'] = true;
            array_push($json['error_messages'], $this->auth->error());
        }

        return json_encode($json);
    }

    /**
     * Get request for logout. 
     * 
     * @param  $req
     * @param  $res
     * @return mixed
     */
    public function getLogout($req, $res)
    {
        unset($_SESSION['user']);
        return $res->withRedirect($this->router->pathFor('home'));
    }
}