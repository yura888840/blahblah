<?php

namespace Crm\Admin;

use Phalcon\Loader,
    Phalcon\Mvc\Dispatcher,
    Phalcon\Mvc\View,
    Phalcon\Mvc\ModuleDefinitionInterface,
    Phalcon\Mvc\View\Engine\Volt as VoltEngine;


class Module implements ModuleDefinitionInterface
{

    public function registerAutoloaders(\Phalcon\DiInterface $di=null)
    {
        $loader = new \Phalcon\Loader();
        
        $loader->registerNamespaces(
            array(
                'Crm\Admin\Controllers' => '../app/admin/controllers/',
                'Crm\Admin\Models'      => '../app/admin/models/',
                'Crm\Admin\Forms' => '../app/admin/forms/',
            )
        );

        $loader->register();
    }

    public function registerServices(\Phalcon\DiInterface $di){

        $di->set('dispatcher', function() use ($di) {
            $evManager = $di->getShared('eventsManager');

            // Плагин безопасности слушает события, инициированные диспетчером
            $evManager->attach('dispatch:beforeExecuteRoute', new \Crm\Permissions\SecurityPlugin());

            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace("Crm\Admin\Controllers");
            $dispatcher->setEventsManager($evManager);
            return $dispatcher;
        },true);

        /**
         * Setting up the view component
         *
         * RE- DEFINITION
         */
       $di->set('view', function () {
           $view = new View();
           $view->setViewsDir('../app/admin/views/');
           $view->registerEngines(array(
               '.volt' => function ($view, $di) {
                   $volt = new VoltEngine($view, $di);

                   $volt->setOptions(array(
                       'compiledPath' => __DIR__ . '/../../app/cache/',
                       'compiledSeparator' => '_',
                       'stat' => true,
                       'compileAlways' => true,
                   ));

                   return $volt;
               },
               '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
           ));

           return $view;
       }, true);
    }

}
