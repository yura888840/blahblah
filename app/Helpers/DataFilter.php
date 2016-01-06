<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 01.05.15
 * Time: 09:50
 */

namespace Crm\Helpers;

class DataFilter
{

    public static $aliasForId = array(
        7 => 'Amazon CA',
        5 => "Main Website\nMobile version",
        1 => "Main Website\nFull version",
        6 => 'Amazon UK',
        null => 'Default Admim',
    );

    public static function getStores()
    {
        $company = \Crm\Models\AnalyticsOrders::aggregate(array(
            array(
                '$project' => array(
                    '_id' => 0,
                    'store_id' => 1,
                    'store_name' => 1,
                )
            ),
            array(
                '$group' => array(
                    '_id' => array('store_id' => '$store_id'),
                    'store_id' => array('$first' => '$store_id'),
                    'store_name' => array('$first' => '$store_name'),
                )
            ),
        ));
        $filter = array(
            array(
                'text'=>'By channel',
                'value'=>0,
            ),
            array(
                'text'=>'All',
                'value'=>0,
            ),
        );
        foreach ($company['result'] as $value) {
            $storeName = $value['store_name'];
            if (isset(self::$aliasForId[$value['store_id']])) {
                $storeName = self::$aliasForId[$value["store_id"]];
            }
            if ($value['store_id'] == null) {
                $value['store_id'] = 'null';
            }
            $filterValue = array (
                'text'=>$storeName,
                'value'=>$value['store_id'],
            );
            $filter[] = $filterValue;
        }
        return $filter;
    }

} 