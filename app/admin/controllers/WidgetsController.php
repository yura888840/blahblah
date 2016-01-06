<?php
namespace Crm\Admin\Controllers;

use Phalcon\Tag;
use Phalcon\Filter;
use Crm\Models\Profiles;
use Crm\Models\WidgetsPermissions;
use Crm\Models\WidgetsDefaultSetGroups;
use Crm\Models\WidgetsDefaultGridGroups;
use Crm\Models\Widgets;
use Sokil\Mongo\Exception;
use Crm\Helpers\WidgetsPermissionsUpdater;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Logger\Adapter\File as FileAdapter;

class WidgetsController extends ControllerBaseAdmin
{
    public function beforeExecuteRoute(Dispatcher $dispatcher)
    {
        $this->autoInstallRemoveWidgets();
        parent::beforeExecuteRoute($dispatcher);
    }

    public function indexAction()
    {
        $perms = WidgetsPermissions::find([[]]);
        $widgets = $this->getWidgets($perms);
        $this->view->setVar('widgets', $widgets);
    }

    private function getWidgets($perms)
    {
        $widgets = [];

        foreach ($perms as $p) {
            foreach ($p as $k => $v) {
                $widgets[$p->name] = $p->permissions;
            }
        }

        $roles = Profiles::find([['active' => 'Y']]);
        $rs = array();
        foreach ($roles as $role) {
            $rs[] = $role->name;
        }

        $installedWidgets = Widgets::find([[]]);
        $preparedInstalledWidgets = [];
        foreach ($installedWidgets as $w) {
            $preparedInstalledWidgets[] = $w->widgetId;
        }
        $installedWidgets = $preparedInstalledWidgets;

        $updateNeeded = false;

        $widgetsIntersection = array_unique(array_merge(array_keys($widgets), $installedWidgets));


        $needCleanup = false;
        foreach ($widgetsIntersection as $intersected) {
            if (!array_key_exists($intersected, $widgets)) {
                $widgets[$intersected] = [];
            }

            if (!in_array($intersected, $installedWidgets)) {
                unset($widgets[$intersected]);
                $needCleanup = true;
            }
        }

        if ($needCleanup) {
            WidgetsPermissionsUpdater::clean($widgets);
        }

        foreach ($widgets as $wName => $wVal) {
            $p = array_keys($wVal);
            $r = array_unique(array_merge($p, $rs));
            foreach ($r as $roleName) {
                if (!array_key_exists($roleName, $wVal)) {
                    $wVal[$roleName] = 0;
                }
                //hack @todo fix array s
                if (!in_array($roleName, $rs)) {
                    unset($wVal[$roleName]);
                    $updateNeeded = true;
                }
            }

            $widgets[$wName] = $wVal;
        }

        if ($updateNeeded) {
            WidgetsPermissionsUpdater::run($widgets);
        }
        return $widgets;
    }

    public function updateAction(){
        $this->view->disable();

        if ($this->request->isPost() && $this->request->isAjax()) {
            $data = $this->request->getPost();

            if (!is_array($data)) {
                throw new \Exception('Error in data');
            }

            foreach ($data as $widgetName => $roles) {
                if (!is_array($roles)) {
                    throw new \Exception('Error in permissions');
                }

                $savedPermissions = WidgetsPermissions::findFirst([['name' => $widgetName]]);

                if (!$savedPermissions) {
                    $savedPermissions = new WidgetsPermissions();
                    $savedPermissions->name = $widgetName;
                }

                $permissions = [];
                foreach ($roles as $role => $actions) {
                    $permissions[$role] = array_key_exists('view', $actions) ? $actions['view'] : 0;
                }

                $savedPermissions->permissions = $permissions;

                if (!$savedPermissions->save()) {
                    throw new \Exception('Error while saving resource permissions');
                }

                header('Content-type: application/json');
                $resp = array('success' => true, 'items' => []);
                echo json_encode($resp);

            }
        } else {
            echo 'This end- point accepts only ajax requests';
        }
    }

    public function defaultAction()
    {

        $perms = WidgetsPermissions::find([[]]);
        $profilesWidgets = WidgetsDefaultSetGroups::find([[]]);
        //@todo garbage collector for inactive roles

        $installedWidgets = Widgets::find([[]]);
        $preparedInstalledWidgets = [];
        foreach ($installedWidgets as $w) {
            $preparedInstalledWidgets[] = $w->widgetId;
        }
        $installedWidgets = $preparedInstalledWidgets;

        $widgets = [];
        foreach ($profilesWidgets as $v) {
            if (!is_array($v->set)) {
                $v->set = [];
                if (!$v->save()) {
                    throw new \Exception('Error while saving profile default set');
                }
            }

            $role = $v->role;
            $list = $v->set;

            foreach ($list as $w) {
                if (!array_key_exists($w, $widgets)) {
                    $widgets[$w] = [];
                }
                $widgets[$w][] = $role;
            }
        }

        $widgetsInRoleswidgets = array_keys($widgets);
        //@todo check in widget was removed
        $widgetsIntersect = array_unique(array_merge($widgetsInRoleswidgets, $installedWidgets));

        $needWidgetsCleanUp = false;
        foreach ($widgetsIntersect as $intersected) {
            if (!array_key_exists($intersected, $widgets)) {
                $widgets[$intersected] = [];
            }

            if (!in_array($intersected, $installedWidgets)) {
                unset($widgets[$intersected]);
                $needWidgetsCleanUp = true;
            }
        }

        if ($needWidgetsCleanUp) {
            WidgetsPermissionsUpdater::clean($widgets);
        }

        // @todo refactoring
        $roles = Profiles::find([['active' => 'Y']]);
        $rs = array();
        foreach ($roles as $role) {
            $rs[] = $role->name;
        }

        // searching for inactive
        foreach ($widgets as $wName => $wVal) {
            $r = array_unique(array_merge($wVal, $rs));
            $selected = [];
            foreach ($r as $roleName) {
                $selected[$roleName] = in_array($roleName, $wVal) ? 1 : 0;

                if (!in_array($roleName, $rs)) {
                    unset($selected[$roleName]);
                }
            }
            $widgets[$wName] = $selected;
        }

        $this->view->setVar('widgets', $widgets);
    }

    CONST WIDGET__ACTION = 'view';

    public function profilesupdateAction()
    {
        $this->view->disable();

        if ($this->request->isPost() && $this->request->isAjax()) {

            $data = $this->request->getPost();

            $profiles = [];
            foreach ($data as $widgetName => $roleData) {
                foreach ($roleData as $roleName => $action) {
                    if (!array_key_exists($roleName, $profiles)) {
                        $profiles[$roleName] = [];
                    }

                    $profiles[$roleName][] = array($widgetName => $action[self::WIDGET__ACTION]);

                }
                $profiles[$roleName] = array_unique($profiles[$roleName]);
            }

            $d = array_keys($profiles);
            foreach ($d as $v) {
                $r = WidgetsDefaultSetGroups::findFirst([['role' => $v]]);

                if (empty($r)) {
                    $o = new WidgetsDefaultSetGroups();
                    $o->role = $v;
                    $o->set = [];

                    if (!$o->save()) {
                        throw new Exception('Error while saving role widgets');
                    }
                }
            }

            $profilesWidgets = WidgetsDefaultSetGroups::find([[]]);

            foreach ($profilesWidgets as $v) {
                if (array_key_exists($v->role, $profiles)) {
                    if (!is_array($v->set)) {
                        $v->set = array();
                    }

                    $widgetNames = array_keys($profiles[$v->role][0]);
                    $widgetName = $widgetNames[0];
                    $widgetPerm = $profiles[$v->role][0][$widgetName];

                    if ($widgetPerm == 1 && !in_array($widgetName, $v->set)) {
                        $v->set[] = $widgetName;
                    } elseif ($widgetPerm == 0 && in_array($widgetName, $v->set)) {
                        $v->set = ksort(array_diff($v->set, array($widgetName)));
                    }

                    ///$logger = new FileAdapter(__DIR__ . "/../../widgets.log");
                    ///$logger->log(serialize($v->list));

                    if (!$v->save()) {
                        throw new Exception('Error saving default widgets');
                    }
                }
            }

            $resp = array('success' => true, 'items' => []);
            echo json_encode($resp);

        } else {
            echo 'This end- point accepts only ajax requests';
        }
    }

    //@todo logger for the new widgets
    public function installAction()
    {
        //$this->view->disable();
        $o = new \Crm\Widget\CrmWidgets('widgets');
        $o->checkAndInstallNew();
    }

    private function autoInstallRemoveWidgets()
    {
        $o = new \Crm\Widget\CrmWidgets('widgets');

        $o->checkAndInstallNew();
    }

    public function defaultGridAction()
    {
        $roleName = $this->dispatcher->getParam(0);

        $role = Profiles::findFirst([['name' => $roleName]]);

        if (empty($role)) {
            return $this->response->redirect('admin/widgets');
        }

        $path = $this->config->custom->assets_path;
        $this->assets
            ->collection('headerDashboardJS')
            ->addJs($path . 'js/d3.v3.min.js')
            ->addJs($path . 'js/nv.d3.js')
            ->addJs($path . 'js/widget.js');

        $this->assets
            ->collection('headerDashboardCSS')
            ->addJs($path . 'css/nv.d3.css');

        $this->view->setVar('role', $roleName);
        $this->loadAdminWidgets($roleName);
    }

    protected function loadAdminWidgets($profile)
    {
        $widgetsList = WidgetsDefaultSetGroups::findFirst([['role' => $profile]]);

        if (!($widgetsList) || !is_array($widgetsList->set)) {
            throw new \Exception('Error in datatype of list of admin widgets. Please contact admin to fix this problem.');
        }

        $unpreparatedWidgets = !empty($widgetsList->set)
            ? Widgets::find([['widgetId' => ['$in' => $widgetsList->set]]])
            : [];
        $widgets = [];

        foreach ($unpreparatedWidgets as $widgetConstructorData) {
            if ($widgetConstructorData->paramsWidget) {
                $widget = \Crm\Widget\WidgetProvider::getWidget($widgetConstructorData->widgetId,
                    $widgetConstructorData->widgetFactoryType,
                    $widgetConstructorData->paramsWidget);
            } else {
                $widget = \Crm\Widget\WidgetProvider::getWidget($widgetConstructorData->widgetId,
                    $widgetConstructorData->widgetFactoryType);
            }

            if (!empty($widget)) {
                $widgets[$widgetConstructorData->widgetId] = array(
                    'widget' => $widget,
                    'id' => $widgetConstructorData->widgetId,
                    'size' => empty($widgetConstructorData->size) ? 1 : $widgetConstructorData->size,
                );
            }
        }

        $data = WidgetsDefaultGridGroups::find([['group' => $profile]]);
        if (count($data) > 1 || empty($data) || !is_array($data) || !array_key_exists(0, $data)) {
            throw new \Exception('Database integrity violation. Check WidgetsDefaultGridGroups collection.');
        }
        $positions = $data;

        $grid = [];
        if (!empty($positions)) {

            $grid = $positions[0]->grid;
            //hack
            if (!array_key_exists(1, $grid)) {
                $grid[1] = [];
            }
            if (!array_key_exists(2, $grid)) {
                $grid[2] = [];
            }

            foreach ($grid as $col => &$w) {
                foreach ($w as $k => $v) {
                    $w[$k] = $widgets[$v];
                }
            }
        }

        $this->view->setVar('grid', $grid);
        foreach ($widgets as $k => $v) {
            $widgets[$k]['position'] = $k + 1;
        }
        $this->view->setVar('widgets', $widgets);
    }
}