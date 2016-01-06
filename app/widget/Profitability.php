<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 04.05.15
 * Time: 19:43
 */

namespace Crm\Widget;


class Profitability extends Chart implements IInstantiable
{

    public function install()
    {
        $return = array(
            'description' => 'Profitability',
            'widgetFactoryType' => 'Chart',
            'paramsWidget' => array(
                'typeWidget' => 'Profitability',
                'typeChart' => 'discreteBarChart',
                'idDiv' => 'chart2'
            ),

            'jsList' => array(// put your values here
            ),
            'css' => array(// put your values here
            ),
            'template' => 'chart',
            'idDiv' => 'chart2', // в параметры
            'typeChart' => '',

        );

        return $return;
    }

}