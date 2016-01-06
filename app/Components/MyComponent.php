<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 18.09.15
 * Time: 16:08
 */

namespace Crm\Components;

use \Phalcon\Events\EventsAwareInterface;

class MyComponent extends ComponentBase implements EventsAwareInterface
{

    public function __construct($listenerName)
    {
        $this->access = $this->accessA;

        parent::__construct($listenerName);
    }


    /**
     * Method for functionallity on ->run() metghod
     *
     */
    public function runAction()
    {

    }


    protected $functionsPriorityStack =
        [
            'getAllObjects',
            'getPrivate',
            'forbidden'
        ];

    // user => allow / deny

    // в конечном счете - правила формируются, для пользователя. Исходя из групповых правил, на которые, "накладываются" правла для пользователя

    // допустим, тут уже получены правила

    /// (тогда, вопрос. Если - расширения идут, то - как ? )

    protected $access = [];

    // Admin
    protected $accessA = [
        'getAllObjects'
    ];

    // правила для обычн пользователя
    protected $accessU = [
        'getPrivate'
    ];

    protected $accessG = [
        'forbidden',
    ];

    public function setAccess($abbrevRole)
    {
        $this->access = $this->{"access" . ucfirst($abbrevRole)};
    }

    public function getPriorityStack()
    {
        return $this->functionsPriorityStack;
    }

    public function getAllObjectsAction()
    {
        echo __METHOD__;
    }


    public function getPrivateObjectsAction()
    {
        echo __METHOD__;
    }

    public function forbiddenAction()
    {
        echo __METHOD__;
    }

    /**
     * Описание
     *
     * В триггерах, нужно указывать - beforeSomeTask, afterSomeTask
     *
     * А, здесь - нужно указывать someTaskAction
     *
     */
}