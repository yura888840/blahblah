<?php

use Crm\Models\Widgets;
use Crm\Models\WidgetsDefaultSetGroups;
use Crm\Models\WidgetsCustomSetUsers;
use Crm\Models\WidgetsCustomGridUsers;
use Crm\Models\WidgetsDefaultGridGroups;

class DashboardController extends ControllerBase
{
    public function indexAction()
    {
        $this->loadWidgets();
        $path = $this->config->custom->assets_path;

        $this->assets
            ->collection('headerDashboardJS')
            ->addJs($path . 'js/d3.v3.min.js')
            ->addJs($path . 'js/nv.d3.js')
            ->addJs($path . 'js/widget.js');

        $this->assets
            ->collection('headerDashboardCSS')
            ->addJs($path . 'css/nv.d3.css');
    }

    protected function loadWidgets()
    {
        $widgetsList = \Crm\Widget\WidgetHelper::widgetsListGridForUser();
        $grid = \Crm\Widget\WidgetHelper::widgetsGridForUser();
        $widgets = \Crm\Widget\WidgetHelper::widgetsForGrid($widgetsList);
        $this->view->setVar('grid', $grid);
        $this->view->setVar('widgets', $widgets);
    }
}

