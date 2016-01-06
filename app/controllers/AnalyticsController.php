<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 27.02.15
 * Time: 18:10
 */

class AnalyticsController extends ControllerBase {

    public function indexAction()
    {
        $path = $this->config->custom->assets_path;

        $this->assets
            ->collection('headerDashboardJS')
            ->addJs($path . 'js/d3.v3.min.js')
            ->addJs($path . 'js/nv.d3.js')
            ->addJs($path . 'js/widget.js');

        $this->assets
            ->collection('headerDashboardCSS')
            ->addJs($path . 'css/nv.d3.css');

        $paramWidget = array(
            'typeWidget' => 'Sales',
            'typeChart' => 'discreteBarChart',
            'idDiv' => 'chart1',
        );
        $widgetSalesContent = Crm\Widget\WidgetProvider::getWidget('widgetSales', 'Chart', $paramWidget);
        $this->view->setVar("widgetSales",$widgetSalesContent);

        $paramWidget = array(
            'typeWidget' => 'Profitability',
            'typeChart' => 'discreteBarChart',
            'idDiv' => 'chart2',
        );
        $widgetProfitabilityContent = Crm\Widget\WidgetProvider::getWidget('widgetProfitability', 'Chart', $paramWidget);
        $this->view->setVar("widgetProfitability",$widgetProfitabilityContent);
    }

}