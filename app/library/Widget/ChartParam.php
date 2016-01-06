<?php
/**
 * Created by PhpStorm.
 * User: Konstantin
 * Date: 09.03.15
 * Time: 19:00
 */

namespace Crm\Widget;


class ChartParam {


    /**
     * Get params for Widget
     *
     * @param string
     * @return array
     */
    public function get ($typeWidget=false) {
        $params=array();
        if (!isset($_GET['type_widget'])){
            $params = array(
                'type_widget' => $typeWidget,
                'indicators' => '0',
                'intervals' => '0',
                'period' => '0',
                'company' => '0',
                'clients' => '0',
                'goods' => '0',
                'type_chart' => 'discreteBarChart',//lineChart or discreteBarChart
            );
            $params['date1']=$this->getDate1($params['period']);
            return $params;
        }

        foreach ($_GET as $key => $param) {
            if ($key!='_url'){
                $params[$key]=$param;
            }
        }

        $params['date1']=$this->getDate1($params['period']);

        $validation = new ChartValidator();

        $messages = $validation->validate($params);
        if (count($messages)) {
//            foreach ($messages as $message) {
//                echo $message, '<br>';
//            }
            return false;
        }
        return $params;
    }

    public function getDate1 ($period) {
        date_default_timezone_set('UTC');
        $dtStart = false;
        $orderMax = \Crm\Models\AnalyticsOrders::findFirst(array(
            "sort" => array("created_at" => -1)
        ));
        if ($orderMax) {
            $dateEnd = date('Y-m-d H:i:s', $orderMax->created_at->sec);
        } else {
            $dateEnd = date('Y-m-d H:i:s');
        }
        if (isset($period)){
            if ($period==0){
                if ($orderMax) {
                    $dt = strtotime ( '-6 month' . $dateEnd );
                    $dtMongoStart = date('Y-m-', $dt).cal_days_in_month(CAL_GREGORIAN, date('m', $dt), date('Y', $dt)).' 23:59:59';
                }
                $dtStart =new \MongoDate(strtotime($dtMongoStart));
            }
            if ($period==3){
                $dtStart = new \MongoDate(strtotime('-1 hour' . $dateEnd));
            }
            if ($period==4){
                $dt = strtotime ( $dateEnd );
                $dtMongoStart = date('Y-m-d', $dt).' 00:00:00';
                $dtStart = new \MongoDate(strtotime($dtMongoStart));
            }
            if ($period==5){
                $dt = strtotime ( $dateEnd );
                $w = (int)date("w", $dt) - 1;
                $dt = strtotime ( '-'.$w.' day' . $dateEnd );
                $dtMongoStart = date('Y-m-d', $dt).' 00:00:00';
                $dtStart = new \MongoDate(strtotime($dtMongoStart));
            }
            if ($period==6){
                $dt = strtotime ( $dateEnd );
                $dtMongoStart = date('Y-m-', $dt).'01 00:00:00';
                $dtStart = new \MongoDate(strtotime($dtMongoStart));
            }
            if ($period==7){
                $dt = strtotime ( $dateEnd );
                $dtMongoStart = date('Y-', $dt).'01-01 00:00:00';
                $dtStart = new \MongoDate(strtotime($dtMongoStart));
            }
        }
        return $dtStart;
    }

} 