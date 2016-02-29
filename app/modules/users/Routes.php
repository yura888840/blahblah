<?php

/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 25.01.16
 * Time: 13:09
 */

namespace users;

class Routes
{
    public function init($router)
    {
        $router->add('/login', array(
            'module'     => 'users',
            'controller' => 'index',
            'action'     => 'login',
        ))->setName('login');

        $router->add('/logout', array(
            'module'     => 'users',
            'controller' => 'index',
            'action'     => 'logout',
        ))->setName('logout');

        $router->add('/register', array(
            'module'     => 'users',
            'controller' => 'index',
            'action'     => 'register',
        ))->setName('register');

        $router->add('/forgotpassword', array(
            'module'     => 'users',
            'controller' => 'index',
            'action'     => 'register',
        ))->setName('forgotpassword');

        return $router;

    }
}