<?php

/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 25.01.16
 * Time: 13:09
 */

namespace Main;

class Routes
{
    public function init($router)
    {
        $router->add('/', array(
            'module'     => 'main',
            'controller' => 'index',
            'action'     => 'index',
        ))->setName('main');

        return $router;

    }
}