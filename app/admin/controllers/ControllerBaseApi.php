<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 16.07.15
 * Time: 13:44
 */

namespace Crm\Admin\Controllers;

class ControllerBaseApi extends \ControllerBase
{
    public function beforeExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher)
    {
        $this->view->disable();
        parent::beforeExecuteRoute($dispatcher);
    }
}