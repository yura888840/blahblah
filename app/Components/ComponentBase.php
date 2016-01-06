<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 18.09.15
 * Time: 16:13
 */

namespace Crm\Components;

use \Phalcon\Events\EventsAwareInterface;

class ComponentBase implements EventsAwareInterface
{

    protected $_eventsManager;
    protected $functionsPriorityStack = [];
    protected $listenerName;

    public $calledMethod = NULL;

    public function __construct($listenerName)
    {
        $this->listenerName = $listenerName;
    }

    public function setEventsManager(\Phalcon\Events\ManagerInterface $eventsManager)
    {
        $this->_eventsManager = $eventsManager;
    }

    public function getEventsManager()
    {
        return $this->_eventsManager;
    }

    public function __call($function, $args)
    {
        if (in_array($function . 'Action', get_class_methods(get_called_class()))) {
            $this->calledMethod = $function;

            $this->_eventsManager->fire($this->listenerName . ":beforeAction", $this);
            $this->_eventsManager->fire($this->listenerName . ":before$function", $this);

            $result = call_user_func_array([$this, $function . 'Action'], $args);

            $this->_eventsManager->fire($this->listenerName . ":afterAction", $this);
            $this->_eventsManager->fire($this->listenerName . ":after$function", $this);

            return $result;
        }
    }

}