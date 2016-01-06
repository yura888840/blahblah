<?php
/**
 * Created by PhpStorm.
 * User: Konstantin
 * Date: 13.03.15
 * Time: 19:20
 */

namespace Crm\MongoSelect;

use Crm\Models\AnalyticsOrders;

class Profitability
{
    /**
     * Get data Profitability
     *
     * @param array
     * @return array
     */
    public function getData ($param) {

        $select = array();
        $indicators = '$items.base_row_total';//default indicators
        $percentY = false;
        $intervals = 7;//default month
        $intervalArray = array('$substr' => array('$created_at',0,$intervals));
        $intervalView = array('$substr' => array('$interval',0,$intervals));
        if (isset($param['indicators']) && $param['indicators']!='0'){
            if ($param['indicators'] == 5){
                $percentY = true;
            }
        }
        if (isset($param['intervals']) && $param['intervals']!='0'){
            $intervals = (int)$param['intervals'];
            switch ($intervals) {
                case 3: //week
                    $intervalArray = array('$week' => '$created_at');
                    $intervalView = array('$concat' => array(array('$substr' => array('$year',0,4)),'W',array('$substr' => array('$week',0,4))));
                    break;
                case 4: //Years
                    $intervalArray = array('$substr' => array('$created_at',0,$intervals));
                    $intervalView = array('$concat' => array(array('$substr' => array('$year',0,4)),'-01-01'));
                    break;
                case 7: //Monthes
                    $intervalArray = array('$substr' => array('$created_at',0,$intervals));
                    $intervalView = array('$substr' => array('$interval',0,$intervals));
                    break;
                case 10: //Days
                    $intervalArray = array('$substr' => array('$created_at',0,$intervals));
                    $intervalView = array('$concat' => array(array(
                        '$substr' => array('$year',0,4)),
                        '-',
                        array('$substr' => array('$month',0,4)),
                        '-',
                        array('$substr' => array('$dayOfMonth',0,4)),
                    ));
                    break;
                case 13: //Hours
                    $intervalArray = array('$substr' => array('$created_at',0,$intervals));
                    $intervalView = array('$concat' => array(array(
                        '$substr' => array('$year',0,4)),
                        '-',
                        array('$substr' => array('$month',0,4)),
                        '-',
                        array('$substr' => array('$dayOfMonth',0,4)),
                        ' ',
                        array('$substr' => array('$hour',0,4)),
                        ':00:00',
                    ));
                    break;

            }
        }
        if ($param['date1']){
            $matchItems['created_at'] = array(
                '$gt' => $param['date1']
            );
        }
        if (isset($param['clients']) && $param['clients']!='0'){
            $matchItems['customer_email'] = $param['clients'];
        }
        if (isset($param['company']) && $param['company']!='0'){
            if ($param['company']==='null') {
                $matchItems['store_id'] = array(
                    '$exists' => false
                );
            } else {
                $matchItems['store_id'] = (int)$param['company'];
            }
        }
        if ($matchItems != array()){
            $select[] = array(
                '$match' => $matchItems
            );
        }
        $select[] = array(
            '$project' => array(
                'created_at' => 1,
                'created_str' =>  array('$dateToString' => array('format' => "%Y-%m-%d %H:%M:%S", 'date' => '$created_at')),
                'year' => array('$year' => '$created_at'),
                'month' => array('$month' => '$created_at'),
                'week' => array('$week' => '$created_at'),
                'dayOfMonth' => array('$dayOfMonth' => '$created_at'),
                'hour' => array('$hour' => '$created_at'),
                'interval' => $intervalArray,
                'store_id' => 1,
                'subtotal' => 1,
                'customer_email' => 1,
                'items.qty_ordered' => 1,
                'items.product_id' => 1,
                'items.price' => 1,
                'items.base_row_total' => 1,
                'items.purchase_price' => 1,
                'items.base_price' => 1,
                'items.original_price' => 1,
                'items.base_original_price' => 1,
            )
        );
        $select[] = array(
            '$unwind' => '$items'
        );
        $select[] = array(
            '$sort' => array(
                'created_at' => 1,
            )
        );

        $matchItems2 = array();
        if (isset($param['goods']) && $param['goods']!='0'){
            $matchItems2['items.product_id'] = (int)$param['goods'];
        }
        $matchItems2['items.base_row_total'] = array(
            '$gt' => 0
        );
        if ($matchItems2 != array()){
            $select[] = array(
                '$match' => $matchItems2
            );
        }

        $select[] = array(
            '$group' => array(
                '_id' => array('interval' => '$interval'),
                'sum' => array('$sum' => $indicators),
                'sumP' => array('$sum' => '$items.purchase_price'),
                'interval' => array('$first' => '$interval'),
                'year' => array('$first' => '$year'),
                'month' => array('$first' => '$month'),
                'week' => array('$first' => '$week'),
                'dayOfMonth' => array('$first' => '$dayOfMonth'),
                'hour' => array('$first' => '$hour'),
            )
        );
        $select[] = array(
            '$sort' => array(
                'year' => 1,
                'interval' => 1,
            )
        );

        $select[] = array(
            '$project' => array(
                '_id' => 1,
                'sum' => 1,
                'sumP' => 1,
                'year' => 1,
                'month' => 1,
                'dayOfMonth' => 1,
                'date' => $intervalView,
            )
        );

        $saleArray = \Crm\Models\AnalyticsOrders::aggregate($select);

        $values = array();
        foreach ($saleArray['result'] as $sale) {
            $date = $sale['date'];
            if (strpos($date, 'W')>0){
                $dateStr1 = substr($date,0,5);
                $dateStr2 = substr($date,5) + 1;
                if (strlen($dateStr2)<2){
                    $dateStr2 = "0".($dateStr2);
                }
                $date = $dateStr1.$dateStr2;
            }
            $date = strtotime($date)*1000;
            $sale["sumP"] = $sale["sum"]*0.7;
            $values[] = array(
                "x" => $date ,
                "y" => ($percentY) ? ($sale["sum"]-$sale["sumP"])/$sale["sumP"] : ($sale["sum"]-$sale["sumP"]),
            );
        }

        $chartDataArray = array(
            array(
                'key'=>'Profitability',
                'values'=>$values
            ),
        );

        return $chartDataArray;

    }


}
