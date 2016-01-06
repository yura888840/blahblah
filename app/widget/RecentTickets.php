<?php

namespace Crm\Widget;


use Crm\Models;

class RecentTickets extends \Phalcon\Mvc\Controller implements \Crm\Widget\IWidget, \Crm\Widget\IInstantiable
{
    public function run($param = array())
    {
        $data = $this->getData();
        $params = array(
            'data' => $data,
        );
        $stringContent=$this->simple_view->render('widget/recentTickets',$params);
        return $stringContent;
    }

    public function install()
    {
        $return = array(
            'description' => 'Recent Tickets',
            'widgetFactoryType' => 'RecentTickets',
            'paramsWidget' => array(
                'typeWidget' => '',
                'typeChart' => '',
                'idDiv' => 'RecentTickets'
            ),

            'jsList' => array(// put your values here
            ),
            'css' => array(// put your values here
            ),
            'template' => 'recentTickets',
            'idDiv' => 'RecentTickets',
            'typeChart' => '',

        );
        return $return;
    }

    public function getData()
    {
        $queryArr = array(
            array(

            ),
            "sort" => array("created" => -1),
            "limit" => 5,
        );
        $tickets = \Crm\Models\Tickets::find($queryArr);
        $ticketsList = array();
        $row = 0;
        foreach ($tickets as $ticket) {
            $row = $row+1;
            $ticketOne = array();
            $ticketOne['id'] = (string)$ticket->_id;
            $ticketOne['subject'] = $ticket->subject;
            $ticketOne['status'] = $ticket->status;
            $ticketOne['created'] = \Crm\Helpers\DateTimeFormatter::format ($ticket->created)['created'];
            $ticketsList[] = $ticketOne;
        }
        return $ticketsList;
    }
}