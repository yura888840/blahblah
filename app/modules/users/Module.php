<?php

namespace users;

use Phalcon\Mvc\ModuleDefinitionInterface;


class Module implements ModuleDefinitionInterface
{

    public function registerAutoloaders(\Phalcon\DiInterface $di=null)
    {
        $loader = new \Phalcon\Loader();

        $loader->registerNamespaces(
            array(
                'users\Controllers' => __DIR__ . '/controllers/',
            )
        );

        $loader->register();
    }

    public function registerServices(\Phalcon\DiInterface $di)
    {
        $dispatcher = $di->get('dispatcher');
        $dispatcher->setDefaultNamespace('users\Controllers');
        $di->set('dispatcher', $dispatcher);

        $view = $di->get('view');
        $view->setViewsDir(__DIR__ .'/views/');
    }

}
