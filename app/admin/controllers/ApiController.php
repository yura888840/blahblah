<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 14.05.15
 * Time: 15:56
 */

namespace Crm\Admin\Controllers;

use \Crm\Models\WidgetsDefaultGridGroups;
use \Crm\Models\Profiles;

class ApiController extends ControllerBaseAdmin
{
    public function widgetssaveAction()
    {
        $this->view->disable();
        $request = new \Phalcon\Http\Request();

        if ($request->isPost() == true && $request->isAjax()) {
            $roleName = $this->dispatcher->getParam(0);

            $role = Profiles::findFirst([['name' => $roleName]]);

            if (empty($role)) {
                $success = false;

                $msg = "Role doesn't exists";

                header('Content-type: application/json');
                echo json_encode(array('success' => $success, 'reason' => $msg));
                exit();
            }

            $widgets = WidgetsDefaultGridGroups::findFirst([['group' => $roleName]]);

            if (!$widgets) {
                $widgets = new WidgetsDefaultGridGroups();

                $widgets->group = $role;

                $widgets->grid = [1 => [], 2 => []];
            }

            $panelData = $request->getPost('obj');
            $panelId = $request->getPost('id');

            if (!$panelId) {
                throw new \Exception('Not enough params in request. Usage: parametrs: id, obj');
            }

            if (empty($panelData)) {
                $panelData = [];
            }

            $origGrid = $widgets->grid[$panelId];
            $widgets->grid[$panelId] = [];

            foreach ($panelData as $k => $v) {
                $widgets->grid[$panelId][] = $v;
            }

            $success = ($widgets->save()) ? true : false;

            echo json_encode(array('success' => $success, 'items' => []));
        } else {
            echo 'This end- point accepts only Ajax requests';
        }
    }
}