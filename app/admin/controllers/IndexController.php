<?php
namespace Crm\Admin\Controllers;

class IndexController extends ControllerBaseAdmin
{
    public function indexAction()
    {
        $path = $this->config->custom->assets_path;

        $this->assets
            ->collection('headerAdminDashboardJS')
            ->addJs($path . 'js/d3.v3.min.js');

        $this->assets
            ->collection('headerAdminDashboardCSS')
            ->addJs($path . 'css/nv.d3.css');
    }
}

