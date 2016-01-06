<?php
/**
 * User: Konstantin
 * Date: 26.02.15
 * Time: 13:11
 */

use Crm\MongoSelect;

class DataProvider {

    public $typeRequest;
    public $params;

    public function getData ($param) {
        $chartDataArray = array();
        if (is_array($param)){
            if (isset($param['type_widget'])){
                if ($param['type_widget']=='Sales'){
                    unset($param['type_widget']);
                    $sale = new MongoSelect\Sale();
                    return $sale->getData($param);
                }
                if ($param['type_widget']=='Profitability'){
                    unset($param['type_widget']);
                    $sale = new MongoSelect\Profitability();
                    return $sale->getData($param);
                }
            }
        }
        return $chartDataArray;
    }
} 