<?php

namespace Crm\Widget;


use Crm\Models;

class OrderAmount extends \Phalcon\Mvc\Controller implements \Crm\Widget\IWidget, \Crm\Widget\IInstantiable
{
    public function run($param = NULL)
    {
        $orderList = array();

        $params = array(
            'orders' => $orderList,
        );
        $stringContent=$this->simple_view->render('widget/orderAmount',$params);
        return $stringContent;
        $queryArr = array(
        array(

            ),
            "sort" => array("created_at" => -1),
            "limit" => 5,
        );
        $ordersFind = \Crm\Models\AnalyticsOrders::find($queryArr);
        $orderList = array();
        $row = 0;
        foreach ($ordersFind as $orderOne) {
            $row = $row + 1;
            $order = array();
            $order['row'] = $row;
            $order['customer_name'] = $orderOne->customer_firstname.' '.$orderOne->customer_lastname;
            $order['subtotal'] = $orderOne->subtotal;
            $order['date'] = \Crm\Helpers\DateTimeFormatter::format ($orderOne->created_at)['created'];
            $orderList[] = $order;
        }
        $params = array(
            'orders' => $orderList,
        );
        $stringContent=$this->simple_view->render('widget/lastOrder',$params);
        return $stringContent;
    }

    public function install()
    {
        $return = array(
            'description' => 'Order Amount',
            'widgetFactoryType' => 'OrderAmount',
            'paramsWidget' => array(),

            'jsList' => array(// put your values here
            ),
            'css' => array(// put your values here
            ),
            'template' => 'chart',
            'idDiv' => '',
            'typeChart' => '',

        );

        return $return;
    }
}