<?php
namespace Crm\Admin\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use Crm\Assets\Js\JsPreprocessor;

class ControllerBaseAdmin extends \ControllerBase
{
    protected function assignTemplateVars()
    {
        $path = $this->config->custom->assets_path;
        $this->assets
            ->collection('headerJs')
            ->addJs($path . 'js/jquery-1.11.2.min.js', true, false)
            ->addJs($path . 'js/bootstrap.min.js', true, false)
            ->addJs($path . 'js/jquery-ui.min.js', true, false)
            ->addJs($path . 'js/jquery.tablesorter.min.js', true, false)
            ->addJs($path . 'js/jquery.tablesorter.widgets.js', true, false)
            ->addJs($path . 'js/jquery.tablesorter.pager.js', true, false)
            ->addJs($path . 'js/jquery.cookie.js', true, false)
            ->addJs($path . 'js/bootstrap-editable.min.js', true, false)
            ->addJs($path . 'js/chosen.jquery.js', true, false)
            ->addJs($path . 'js/jquery.validate.min.js', true, false)
            ->addJs($path . 'js/additional-methods.min.js', true, false)
            ->addJs($path . 'js/single-ajax-interface.js', true, false)
            ->addJs($path . 'js/custom-ajax-form.js', true, false)
            ->addJs($path . 'js/custom-ajax-tablesorter.js', true, false)
            ->addJs($path . 'js/custom-ajax-auto-reschanges.js', true, false)
            ->addJs($path . 'js/custom-ajax-widgets.js',true, false)
            ->addJs($path . 'js/bootstrap3-wysihtml5.all.min.js', true, false)
            ->addJs($path . 'js/jq_admin.js', true, false)
            ->addJs($path . 'js/admin_init.js', true, false);

        $this->assets
            ->collection('headerJsIe')
            ->addJs($path . 'js/html5shiv.js')
            ->addJs($path . 'js/respond.min.js');

        $this->assets
            ->collection('headerCss')
            ->addCss($path . 'css/bootstrap.css')
            ->addCss($path . 'css/font-awesome.min.css')
            ->addCss($path . 'css/bootstrap-editable.css')
            ->addCss($path . 'css/animate.css')
            ->addCss($path . 'css/table.css')
            ->addCss($path . 'css/style.css')
            ->addCss($path . 'css/widget.css');

        $this->assets
            ->collection('headerCssCalendar')
            ->addCss($path . 'css/fullcalendar.css');
        $this->assets
            ->collection('headerJsCalendar')
            ->addJs($path . 'js/moment.min.js')
            ->addJs($path . 'js/fullcalendar.min.js');

        $this->assets
            ->collection('headerCssViewTicket')
            ->addCss($path . 'css/dropzone.css');
        $this->assets
            ->collection('headerJsViewTicket')
            ->addJs($path . 'js/dropzone.js');

        $this->view->setVar('assets_path', $this->config->custom->assets_path);
        $this->view->setVar('assets_path_tpl', $this->config->custom->assets_path_tpl);
        $this->view->setVar('baseuri', $this->url->getBaseUri());

        // identity
        $identity = $this->auth->getIdentity();
        $name = (is_array($identity) && array_key_exists('name', $identity)) ? $identity['name'] : '';
        $this->view->setVar("username", $name);
        $this->view->setVar('socketDomain', $this->config->webSocket->domain);

        // Placeholder for common variables
        $vars = array();
        $this->view->setVar('vars', $vars);
    }
}