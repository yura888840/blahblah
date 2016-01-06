<?php

/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 02.07.15
 * Time: 17:44
 */
class ApiTicketsAttachmentsOldController extends ControllerBase
{

    public function ticketAttachAction($p = null)
    {
        $this->view->disable();

        \Crm\Helpers\AttachHelper::saveAttachDropzone(new \Crm\Models\Tickets, $p);
    }


    /**
     *
     *
     * @perm_type: getList
     */
    public function reloadTicketAttachAction()
    {
        $this->view->disable();
        \Crm\Helpers\AttachHelper::reloadExistAttachDropzone(new \Crm\Models\Tickets);
    }

    /**
     * В Post - е :
     * uniqName - уникальное имя
     * parent_id - modelID
     *
     * Этот хэлпер определяет, к какой модели аттач, по переданному имени Модели
     *
     * @perm_type: delete
     */
    public function removeOneTicketAttachAction()
    {
        $this->view->disable();
        \Crm\Helpers\AttachHelper::removeOneAttachDropzone(new \Crm\Models\Tickets);
    }


}