<?php
/**
 * Created by PhpStorm.
 * User: Konstantin
 * Date: 11.03.15
 * Time: 13:00
 */

namespace Crm\MongoSelect;


class ProfitProduct
{

    public function getData ($params = array()) {

        $params = $this->getParamsValidate($params);
        if ($params == array()){
            return array();
        }
        $select = array();
        $matchItems['created_at'] = array(
            '$gt' => new \MongoDate(strtotime('-1 day' . $params['date1'].' 23:59:59')),
            '$lt' => new \MongoDate(strtotime('+1 day' . $params['date2'].' 00:00:00'))
        );
        $matchItems['status'] = array(
            '$eq' => 'complete'
        );
        if ($params['company']==='null') {
            $matchItems['store_id'] = array(
                '$exists' => false
            );
        } else {
            if ($params['company']>0) {
                $matchItems['store_id'] = $params['company'];
            }
        }
        if ($matchItems != array()){
            $select[] = array(
                '$match' => $matchItems
            );
        }
        $select[] = array(
            '$project' => array(
                '_id' => 0,
                'status' => 1,
                'store_id' => 1,
                'store_name' => 1,
                'created_at' => 1,
                'items.qty_ordered' => 1,
                'items.qty_refunded' => 1,
                'items.price' => 1,
                'items.row_total' => 1,
                'items.amount_refunded' => 1,
                'items.product_id' => 1,
                'items.sku' => 1,
                'items.name' => 1,
                'grand_total' => 1,
                'subtotal' => 1,
                'shipping_amount' => 1,
                'tax_amount' => 1,
                'discount_amount' => 1,
                'items.discount_amount' => 1,
            )
        );
        $select[] = array(
            '$unwind' => '$items'
        );
        $select[] = array(
            '$project' => array(
                'created_at' => 1,
                'status' => 1,
                'store_id' => 1,
                'store_name' => 1,
                'items.qty_ordered' => 1,
                'items.qty_refunded' => 1,
                'items.price' => 1,
                'items.row_total' => 1,
                'items.product_id' => 1,
                'items.sku' => 1,
                'items.name' => 1,
                'grand_total' => 1,
                'subtotal' => 1,
                'shipping_amount' => 1,
                'tax_amount' => 1,
                'discount_amount' => 1,
                'items.discount_amount' => 1,
                'total' => array(
                    '$multiply' => array(
                        array(
                            '$subtract' => array(
                                '$items.qty_ordered',
                                '$items.qty_refunded'
                            )
                        ),
                        array(
                            '$subtract' => array(
                                '$items.price',
                                array(
                                    '$divide' => array(
                                        '$items.discount_amount',
                                        '$items.qty_ordered'
                                    )
                                )
                            )
                        )
                    )
                ),
            )
        );
        $select[] = array(
            '$group' => array(
                '_id' => array('product_id' => '$items.product_id'),
                'store_id' => array('$first' => '$store_id'),
                'store_name' => array('$first' => '$store_name'),
                'sumQty' => array('$sum' => '$items.qty_ordered'),
                'sumTotal' => array('$sum' => '$total'),
                'sku' => array('$first' => '$items.sku'),
                'name' => array('$first' => '$items.name'),
            )
        );
        $matchItems2['sumTotal'] = array(
            '$gt' => 0
        );

        if (isset($params['filter'])){
            if (isset($params['filter']['0'])) {
                $search_string = $params['filter']['0'];
                $matchItems2['sku'] = new \MongoRegex("/$search_string/");
            }
            if (isset($params['filter']['1'])) {
                $search_string = $params['filter']['1'];
                $matchItems2['name'] = new \MongoRegex("/$search_string/i");
            }
        }
        $select[] = array(
            '$match' => $matchItems2
        );

        $sort = array();
        if (isset($params['column'])){
            if (isset($params['column'][0])){
                if ($params['column'][0] == '0'){
                    $sort['sku'] = -1;
                } else {
                    $sort['sku'] = 1;
                }
            }
            if (isset($params['column'][1])){
                if ($params['column'][1] == '0'){
                    $sort['name'] = -1;
                } else {
                    $sort['name'] = 1;
                }
            }
            if (isset($params['column'][2])){
                if ($params['column'][2] == '0'){
                    $sort['sumTotal'] = -1;
                } else {
                    $sort['sumTotal'] = 1;
                }
            }
        }
        if ($sort == array()){
            $sort['sumTotal'] = -1;
        }
        $select[] = array(
            '$sort' => $sort
        );

        $skip = $params['size']*$params['page'];
        $limit = $params['size'];

        $saleArray = \Crm\Models\AnalyticsOrders::aggregate($select);
        if (isset($saleArray['result'])) {
            $result = array();
            $headers = array(
                'Sku',
                'Product Name',
                'Profit($)',
            );
            $rows = array();

            $sumTotal = 0;
            foreach ($saleArray['result'] as $key => $row) {
                $sumProfit = $row['sumTotal']*$params['percentProfit']/100;
                if ((($key+1)>$skip) and (($skip+$limit)>$key)) {
                    $rowResult['Sku'] = '<span id="'.$row["_id"]["product_id"].'">'.$row['sku'].'</span>';
                    $rowResult['Product Name'] = $row['name'];
                    $rowResult['Profit($)'] = round($sumProfit, 2);
                    $rows[] = $rowResult;
                }
                $sumTotal = $sumTotal + $sumProfit;
            }

            $result['headers'] = $headers;
            $result['rows'] = $rows;
            $result['total_rows'] = count($saleArray['result']);
            $result['sum_total'] = round($sumTotal, 2);
            return $result;
        } else {
            return array();
        }

    }

    public function getDataProductChannels ($params = array()) {


        $params = $this->getParamsValidate($params);
        if ($params == array()){
            return array();
        }
        $select = array();
        $matchItems['created_at'] = array(
            '$gt' => new \MongoDate(strtotime('-1 day' . $params['date1'].' 23:59:59')),
            '$lt' => new \MongoDate(strtotime('+1 day' . $params['date2'].' 00:00:00'))
        );
        $matchItems['status'] = array(
            '$eq' => 'complete'
        );
        $matchItems['items.product_id'] = (int)$params['product_id'];
        if ($matchItems != array()){
            $select[] = array(
                '$match' => $matchItems
            );
        }
        $select[] = array(
            '$project' => array(
                '_id' => 0,
                'status' => 1,
                'store_id' => 1,
                'store_name' => 1,
                'created_at' => 1,
                'items.qty_ordered' => 1,
                'items.qty_refunded' => 1,
                'items.price' => 1,
                'items.row_total' => 1,
                'items.amount_refunded' => 1,
                'items.product_id' => 1,
                'items.sku' => 1,
                'items.name' => 1,
                'grand_total' => 1,
                'subtotal' => 1,
                'shipping_amount' => 1,
                'tax_amount' => 1,
                'discount_amount' => 1,
                'items.discount_amount' => 1,
            )
        );
        $select[] = array(
            '$unwind' => '$items'
        );
        $matchItems1['items.product_id'] = (int)$params['product_id'];
        $select[] = array(
            '$match' => $matchItems1
        );
        $select[] = array(
            '$project' => array(
                'created_at' => 1,
                'status' => 1,
                'store_id' => 1,
                'store_name' => 1,
                'items.qty_ordered' => 1,
                'items.qty_refunded' => 1,
                'items.price' => 1,
                'items.row_total' => 1,
                'items.product_id' => 1,
                'items.sku' => 1,
                'items.name' => 1,
                'grand_total' => 1,
                'subtotal' => 1,
                'shipping_amount' => 1,
                'tax_amount' => 1,
                'discount_amount' => 1,
                'items.discount_amount' => 1,
                'total' => array(
                    '$multiply' => array(
                        array(
                            '$subtract' => array(
                                '$items.qty_ordered',
                                '$items.qty_refunded'
                            )
                        ),
                        array(
                            '$subtract' => array(
                                '$items.price',
                                array(
                                    '$divide' => array(
                                        '$items.discount_amount',
                                        '$items.qty_ordered'
                                    )
                                )
                            )
                        )
                    )
                ),
            )
        );
        $select[] = array(
            '$group' => array(
                '_id' => array('store_id' => '$store_id'),
                'store_id' => array('$first' => '$store_id'),
                'product_id' => array('$first' => '$items.product_id'),
                'store_name' => array('$first' => '$store_name'),
                'sumQty' => array('$sum' => '$items.qty_ordered'),
                'sumTotal' => array('$sum' => '$total'),
                'sku' => array('$first' => '$items.sku'),
                'name' => array('$first' => '$items.name'),
            )
        );
        $matchItems2['sumTotal'] = array(
            '$gt' => 0
        );

        $select[] = array(
            '$match' => $matchItems2
        );

        $sort = array();
        if ($sort == array()){
            $sort['sumTotal'] = -1;
        }
        $select[] = array(
            '$sort' => $sort
        );

        $saleArray = \Crm\Models\AnalyticsOrders::aggregate($select);

        $resultTable = '<table class="table">
                <thead>
                <tr>
                    <th>Channel</th>
                    <th>Profit($)</th>
                    <th>Profit(%)</th>
                </tr>
                </thead>
                <tbody>';

        if (isset($saleArray['result'])) {
            $storeAliasForId = \Crm\Helpers\DataFilter::$aliasForId;
            $sumTotal = 0;
            foreach ($saleArray['result'] as $key => $row) {
                $sumTotal = $sumTotal + $row['sumTotal']*$params['percentProfit']/100;
            }
            foreach ($saleArray['result'] as $key => $row) {
                if (isset($storeAliasForId[$row["store_id"]])) {
                    $storeName = $storeAliasForId[$row["store_id"]];
                } else {
                    $storeName = $row["store_name"];
                }
                $sum = $row['sumTotal']*$params['percentProfit']/100;
                $resultTable .= '<tr>
                    <td>'.$storeName.'</td>
                    <td>'.round($sum, 2).'$</td>
                    <td>'.round(($sum/$sumTotal)*100, 2).'%</td>
                    </tr>';
            }
            $resultTable .= '</tbody>
            </table>';
            return $resultTable;
        } else {
            return 'No data';
        }
    }

    public function getParamsValidate ($params = array()) {
        if (isset($params['size'])) {
            $params['size'] = (int)$params['size'];
        } else {
            $params['size'] = 10;
        }

        if (isset($params['page'])) {
            $params['page'] = (int)$params['page'];
        } else {
            $params['page'] = 0;
        }

        if (!isset($params['date1'])) {
            $params['date1'] = date('Y').'-01-01';
        }
        if (!isset($params['date2'])) {
            $params['date2'] = date('Y').'-01-31';
        }
        if (isset($params['company'])) {
            if ($params['company'] == 'null') {
                $params['company'] = $params['company'];
            } else {
                $params['company'] = (int)$params['company'];
            }
        } else {
            $params['company'] = 0;
        }

        if (isset($params['percentProfit'])) {
            $params['percentProfit'] = (int)$params['percentProfit'];
        } else {
            $params['percentProfit'] = 30;
        }

        return $params;

    }
}
