<?php

namespace Crm\Widget;


class Chart extends \Phalcon\Mvc\Controller implements \Crm\Widget\IWidget
{
    const lifetime = 14400;//время хранения кэша

    public function run($param)
    {
        if (isset($param['typeWidget'])){
            $typeWidget = $param['typeWidget'];
        }else{
            return '';
        }
        if (isset($param['typeChart'])){
            $typeChart = $param['typeChart'];
        }else{
            return '';
        }
        if (isset($param['idDiv'])){
            $idDiv = $param['idDiv'];
        }else{
            return '';
        }

        $widgetData = new ChartData();
        $widgetData->typeWidget=$typeWidget;
        $widgetData->typeChart=$typeChart;

        //@todo продумать и реализовать "умный" сброс кэша
        // Кэшируем данные графика
        $frontCache = new \Phalcon\Cache\Frontend\Data(array(
            "lifetime" => self::lifetime
        ));
        // Создаем компонент, который будем кэшировать из "Выходных данных" в "Файл"
        // Устанавливаем папку для кэшируемых файлов - важно сохранить символ "/" в конце пути
        $cache = new \Phalcon\Cache\Backend\File($frontCache, array(
            "cacheDir" => "../app/cache/"
        ));
        // Пробуем получить закэшированный график
        $cacheKey = $typeWidget."-".$typeChart.'.cache';
        $content    = $cache->get($cacheKey);
        if ($content) {
            return $content;
        }

        $filter1 = $widgetData->getFilterList1();
        $filter2 = $widgetData->getFilterList2();
        $filter3 = $widgetData->getFilterList3();
        $filter4 = $widgetData->getFilterList4();
        $filter5 = $widgetData->getFilterList5();
        $filter6 = $widgetData->getFilterList6();
        $chartDataArray = $widgetData->getData(new \DataProvider());

        $params=array(
            'name'=>$widgetData->getName(),
            'filter1'=>$filter1,
            'filter2'=>$filter2,
            'filter3'=>$filter3,
            'filter4'=>$filter4,
            'filter5'=>$filter5,
            'filter6'=>$filter6,
            'typeWidget'=>$typeWidget,
            'typeChart'=>$typeChart,
            'idDiv'=>$idDiv,
            'chartDataJson'=>json_encode($chartDataArray),
        );

        $stringContent=$this->simple_view->render('widget/chart',$params);

        // Сохраняем их в кэше
        $cache->save($cacheKey, $stringContent);

        return $stringContent;
    }

}