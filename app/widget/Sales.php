<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 04.05.15
 * Time: 19:01
 */

namespace Crm\Widget;


class Sales extends Chart implements IInstantiable
{

    public function install()
    {
        $return = array(
            'description' => 'Sales widget',
            'widgetFactoryType' => 'Chart',
            'paramsWidget' => array(
                'typeWidget' => 'Sales',
                'typeChart' => 'discreteBarChart',
                'idDiv' => 'chart1'
            ),

            'jsList' => array(// put your values here
            ),
            'css' => array(// put your values here
            ),
            'template' => 'chart',
            'idDiv' => 'chart1', // это вынести - параметры виджета
            'typeChart' => '', // это тоже - параметры виджета

        );

        return $return;
    }

}