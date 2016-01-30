<?php

/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 22.01.16
 * Time: 15:38
 */
namespace App\MVC;

use \Phalcon\Mvc\Router;

class DefaultRouter extends Router
{

    const ML_PREFIX = 'ml__';

    public function __construct()
    {
        parent::__construct();


        $this->setDefaultController('index');
        $this->setDefaultAction('index');
        $this->setDefaultModule('main');

        $this->add('/:module/:controller/:action/:params', [
            'module' => 1,
            'controller' => 2,
            'action' => 3,
            'params' => 4
        ])->setName('default');
        $this->add('/:module/:controller', [
            'module' => 1,
            'controller' => 2,
            'action' => 'index',
        ])->setName('default_action');
        $this->add('/:module', [
            'module' => 1,
            'controller' => 'index',
            'action' => 'index',
        ])->setName('default_controller');

    }

    public function addML($pattern, $paths = null, $name)
    {
        $iso = 'en_EN';

        $this->add($pattern, $paths)->setName(self::ML_PREFIX . $name . '_' . $iso);
    }

}