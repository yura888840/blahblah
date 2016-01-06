<?php

namespace Crm\Widget;


class ProfitAnalytic extends \Phalcon\Mvc\Controller implements \Crm\Widget\IWidget, \Crm\Widget\IInstantiable
{
    public function run($param)
    {
        $profit = new \Crm\MongoSelect\ProfitProduct();
        $profitData = $profit->getData();
        $stores = \Crm\Helpers\DataFilter::getStores();

        $params=array(
            'dateStart' => date('m/d/Y',$this->getParam('dateStart')),
            'dateEnd' => date('m/d/Y',$this->getParam('dateEnd')),
            'percentProfit' => $this->getParam('percentProfit'),
            'profitData' => $profitData,
            'stores' => $stores,
            'baseUri' => $this->url->getBaseUri(),
        );

        $stringContent=$this->simple_view->render('widget/profitAnalytic',$params);

        return $stringContent;
    }

    private function getParam ($paramName)
    {
        $report = \Phalcon\DI::getDefault()->get('config')->report;
        if ($paramName == 'dateStart') {
            $dateStart = strtotime($report->date_start);
            return $dateStart;
        }
        if ($paramName == 'dateEnd') {
            $dateEnd = strtotime($report->date_end);
            return $dateEnd;
        }
        if ($paramName == 'percentProfit') {
            $percentProfit = $report->percent_profit;
            return $percentProfit;
        }
    }

    public function install()
    {
        $return = array(
            'description' => 'Profit Analytics',
            'widgetFactoryType' => 'ProfitAnalytic',
            'paramsWidget' => '',

            'jsList' => array(// put your values here
            ),
            'css' => array(// put your values here
            ),
            'template' => 'profitAnalytic',
            'idDiv' => '',
            'typeChart' => '',
        );

        return $return;
    }
}