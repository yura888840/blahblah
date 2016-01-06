<?php

namespace Crm\Widget;


use Crm\Models;

class OpenTicketsByPriority extends \Phalcon\Mvc\Controller implements \Crm\Widget\IWidget, \Crm\Widget\IInstantiable
{
    public function run($param = array())
    {
        $data = $this->getData();
        $data = json_encode($data);
        $params = array(
            'data' => $data,
        );
        $stringContent=$this->simple_view->render('widget/openTicketsByPriority',$params);
        return $stringContent;
    }

    public function install()
    {
        $return = array(
            'description' => 'Open Tickets By Priority',
            'widgetFactoryType' => 'OpenTicketsByPriority',
            'paramsWidget' => array(
                'typeWidget' => '',
                'typeChart' => '',
                'idDiv' => 'OpenTicketsByPriority'
            ),

            'jsList' => array(// put your values here
            ),
            'css' => array(// put your values here
            ),
            'template' => 'openTicketsByPriority',
            'idDiv' => 'OpenTicketsByPriority',
            'typeChart' => '',

        );

        return $return;
    }

    public function getData()
    {
        $select[] = array(
            '$sort' => array(
                'priority' => -1,
            )
        );
        $matchItems['status'] = array(
            '$nin' => array(
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
                'count' => array('$sum' => 1),
            )
        );
        $tickets = \Crm\Models\Tickets::aggregate($select);
        $result = array();
        foreach ($tickets['result'] as $priority) {
            if (is_numeric($priority['priority'])){
                $key = 'Priority '.$priority['priority'];
            } else {
                $key = $priority['priority'];
            }
            $result[] = array(
                'key' => $key,
                'y' => $priority['count'],
            );
            $priority2 = 1;
        }
        return $result;
    }
}