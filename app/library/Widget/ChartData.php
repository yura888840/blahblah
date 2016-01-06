<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 25.02.15
 * Time: 23:53
 */

namespace Crm\Widget;

class ChartData {

    public $typeWidget;
    public $typeChart;

    public function getName () {
        switch ($this->typeWidget) {
            case 'Sales':
                return "Sales report";
                break;
            case 'Profitability':
                return "Profitability report";
                break;
        }
    }

    public function getFilterList1 () {
        $filterSales = array(
            array(
                'text'=>'By indicators',
                'value'=>0,
            ),
            array(
                'text' => 'Total sales - $USA',
                'value' => 1,
            ),
//            array(
//                'text'=>'Total sales - $USA per 1 client',
//                'value'=>2,
//            ),
            array(
                'text'=>'Total sales - items',
                'value'=>3,
            ),
//            array(
//                'text'=>'Total sales - items per 1 client',
//                'value'=>4,
//            ),
        );

        $filterProfitability = array(
            array(
                'text'=>'By indicators',
                'value'=>0,
            ),
            array(
                'text'=>'Profitability - $USA',
                'value'=>1,
            ),
            array(
                'text'=>'Profitability - %',
                'value'=>5,
            ),
        );

        switch ($this->typeWidget) {
            case 'Sales':
                $filter = $filterSales;
                break;
            case 'Profitability':
                $filter = $filterProfitability;
                break;
        }
        return $filter;
    }

    public function getFilterList2 () {
        $filter = array(
            array(
                'text'=>'By intervals',
                'value'=>0,
            ),
            array(
                'text'=>'Hours',
                'value'=>13,
            ),
            array(
                'text'=>'Days',
                'value'=>10,
            ),
            array(
                'text'=>'Weeks',
                'value'=>3,
            ),
            array(
                'text'=>'Monthes',
                'value'=>7,
            ),
            array(
                'text'=>'Years',
                'value'=>4,
            ),
        );
        return $filter;
    }

    public function getFilterList3 () {
        $filter = array(
            array(
                'text'=>'By period',
                'value'=>0,
            ),
            array(
                'text'=>'All period',
                'value'=>0,
            ),
            array(
                'text'=>'Last hour',
                'value'=>3,
            ),
            array(
                'text'=>'Last day',
                'value'=>4,
            ),
            array(
                'text'=>'Last week',
                'value'=>5,
            ),
            array(
                'text'=>'Last month',
                'value'=>6,
            ),
            array(
                'text'=>'Last year',
                'value'=>7,
            ),
        );
        return $filter;
    }

    public function getFilterList4 () {
        $stores = \Crm\Helpers\DataFilter::getStores();
        return $stores;
    }

    public function getFilterList5 () {
        $param = new ChartParam();
        $params = $param->get($this->typeWidget);
        $clients = \Crm\Models\AnalyticsOrders::aggregate(array(
            array(
                '$match' => array(
                    'created_at' => array(
                        '$gt' => $params['date1']
                    )
                )
            ),
            array(
                '$project' => array(
                    '_id' => 0,
                    'customer_email' => 1,
                    'customer_firstname' => 1,
                    'customer_lastname' => 1,
                    'subtotal' => 1,
                )
            ),
            array(
                '$group' => array(
                    '_id' => array('customer_email' => '$customer_email'),
                    'customer_firstname' => array('$first' => '$customer_firstname'),
                    'customer_lastname' => array('$first' => '$customer_lastname'),
                    'customer_email' => array('$first' => '$customer_email'),
                    'summa' => array('$sum' => '$subtotal'),
                )
            ),
            array(
                '$sort' => array('summa' => -1)
            ),
            array(
                '$limit' => 100
            ),
        ));
        $filterClient = array(
            array(
                'text'=>'By clients',
                'value'=>0,
            ),
            array(
                'text'=>'All',
                'value'=>0,
            ),
        );
        foreach ($clients['result'] as $value) {
            $filter = array (
                'text'=>$value['customer_firstname'].' '.$value['customer_lastname'],
                'value'=>$value['customer_email'],
            );
            $filterClient[] = $filter;
        }
        return $filterClient;
    }

    public function getFilterList6 () {
        $param = new ChartParam();
        $params = $param->get($this->typeWidget);
        $products = \Crm\Models\AnalyticsOrders::aggregate(array(
            array(
                '$match' => array(
                    'created_at' => array(
                        '$gt' => $params['date1']
                    )
                )
            ),
            array(
                '$project' => array(
                    'items.product_id' => 1,
                    'items.name' => 1,
                    'items.sku' => 1,
                    'subtotal' => 1,
                )
            ),
            array(
                '$unwind' => '$items'
            ),
            array(
                '$group' => array(
                    '_id' => array('product_id' => '$items.product_id'),
                    'product_id' => array('$first' => '$items.product_id'),
                    'name' => array('$first' => '$items.name'),
                    'sku' => array('$first' => '$items.sku'),
                    'summa' => array('$sum' => '$subtotal'),
                )
            ),
            array(
                '$sort' => array('summa' => -1)
            ),
            array(
                '$limit' => 100,
            ),
        ));

        $filter = array(
            array(
                'text'=>'By goods',
                'value'=>0,
            ),
            array(
                'text'=>'All',
                'value'=>0,
            ),
        );
        foreach ($products['result'] as $value) {
            $filterValue = array (
                'text'=>$value['sku'].': '.$value['name'],
                'value'=>$value['product_id'],
            );
            $filter[] = $filterValue;
        }
        return $filter;
    }

    public function getData ($dataProvider) {
        $param = new ChartParam();
        $params = $param->get($this->typeWidget);
        $dataArray = array();
        if ($params){
            if ($this->typeChart=='discreteBarChart'){
                $dataArray=$dataProvider->getData($params);
            }elseif($this->typeChart=='lineChart'){
                $dataArray=$dataProvider->getData($params);
                $dataArray[0]['color']='#ff7f0e';//color line chart
                $dataArray[0]['area']=false;
            }
        }
        return $dataArray;
    }
} 