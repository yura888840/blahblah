<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 18.09.15
 * Time: 19:03
 */

namespace Crm\Core;

class Component
{
    private $component;

    public function __construct($component = NULL)
    {
        if (!$component) return;

        $class = "\\Crm\\Components\\{$component}Component";

        $eventsManager = new \Phalcon\Events\Manager();

        $myComponent = new $class($component);

        $myComponent->setEventsManager($eventsManager);

        $eventsManager->attach($component, new \Crm\Listeners\MyListener());

        $this->component = $myComponent;
    }

    public function init()
    {
        return $this->component;
    }
}