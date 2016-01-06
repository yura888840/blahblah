<?php

namespace Crm\Widget;


use Crm\Models;

class TicketsClosureTimeByPriority extends \Phalcon\Mvc\Controller implements \Crm\Widget\IWidget, \Crm\Widget\IInstantiable
{
    public function run($param = array())
    {
        $data = $this->getData();
        $data = json_encode($data);
        $params = array(
            'data' => $data,
        );
        $stringContent=$this->simple_view->render('widget/ticketsClosureTimeByPriority',$params);
        return $stringContent;
    }

    public function install()
    {
        $return = array(
            'description' => 'Tickets Closure Time By Priority',
            'widgetFactoryType' => 'TicketsClosureTimeByPriority',
            'paramsWidget' => array(
                'typeWidget' => '',
                'typeChart' => '',
                'idDiv' => 'TicketsClosureTimeByPriority'
            ),

            'jsList' => array(// put your values here
            ),
            'css' => array(// put your values here
            ),
            'template' => 'ticketsClosureTimeByPriority',
            'idDiv' => 'TicketsClosureTimeByPriority',
            'typeChart' => '',

        );

        return $return;
    }

    public function getData()
    {
        $select[] = array(
            '$sort' => array(
                'priority' => 1,
            )
        );
        $matchItems['status'] = array(
            '$in' => array(
                'Closed',
            )
        );
        $select[] = array(
            '$match' => $matchItems
        );
        $select[] = array(
            '$group' => array(
                '_id' => array('priority' => '$priority'),
                'priority' => array('$first' => '$priority'),
                'closed_interval' => array('$avg' => '$closed_interval'),
            )
        );
        $tickets = \Crm\Models\Tickets::aggregate($select);
        $result = array();
        $result[] = array(
            'Priority',
            'Days'
        );

        $header = array('X');
        $rowNull = array('X');
        foreach ($tickets['result'] as $priority) {
            $header[] = (string)$priority['priority'];
            $rowNull[] = null;
        }

        $rows = array();
        $rows[] = $header;
        foreach ($tickets['result'] as $keyR => $priority) {
            $row = $rowNull;
            $row[0] = $priority['priority'];
            $row[$keyR+1] = $priority['closed_interval'];
            $rows[] = $row;
        }
        $result = $rows;
        return $result;
    }
}