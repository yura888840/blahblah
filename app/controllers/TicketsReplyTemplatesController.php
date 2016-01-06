<?php

/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 18.06.15
 * Time: 14:45
 */
class TicketsReplyTemplatesController extends ControllerBase
{

    public function indexAction()
    {
        $tpls = \Crm\Models\Replytemplates::find([[]]);

        $this->view->tpls = $tpls;

    }

    public function editAction()
    {
        $tplID = $this->dispatcher->getParam(0);

        if (!$tplID) {
            throw new \Exception('Incorrect ticket object id');
        }

        $template = \Crm\Models\Replytemplates::findById($tplID);

        if (!$template) {
            throw new \Exception('Ticket with specified Id doesn\'t exists');
        }

        $this->view->tpl = $template;

    }

}