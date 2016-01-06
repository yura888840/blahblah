<?php
/**
 * Created by PhpStorm.
 * User: kostja
 * Date: 15.05.15
 * Time: 1:29
 */

namespace Crm\Widget;

class WidgetHelper {

    //Возвращаеи список виджетов на сетке, для пользователя
    static function widgetsListGridForUser()
    {
        $widgetsGridForUser = self::widgetsGridForUser();
        $widgetGrid = array();
        foreach ($widgetsGridForUser as $val) {
            foreach ($val as $v) {
                if (class_exists ('\\Crm\\Widget\\'.$v)) {
                    $widgetGrid[] = $v;
                }
            }
        }
        return $widgetGrid;


        $auth = new \Crm\Auth\Auth();
        $user = $auth->getName();
        $widgetGridOb = \Crm\Models\WidgetsCustomGridUsers::find([['user' => $user]]);
        $widgetGrid = array();
        foreach ($widgetGridOb[0]->grid as $val) {
            foreach ($val as $v) {
                $widgetGrid[] = $v;
            }
        }
        return $widgetGrid;
    }

    //Возвращаеи список разрешенных виджетов, для пользователя
    static function widgetsListForUser()
    {
        $auth = new \Crm\Auth\Auth();
        $profile = $auth->getProfile();
        $widgetGroupOb = \Crm\Models\WidgetsPermissions::find([['permissions.'.$profile => '1']]);
        $widgetGroup = array();
        foreach ($widgetGroupOb as $val) {
            if (class_exists ('\\Crm\\Widget\\'.$val->name)) {
                $widgetGroup[] = $val->name;
            }
        }
        return $widgetGroup;
    }

    //Возвращаеи список виджетов доступных для добавления на сетку для пользователя
    static function widgetsToAdd()
    {
        $widgetGrid = self::widgetsListGridForUser();
        $widgetGroup = self::widgetsListForUser();
        $widgetAdd = array_diff($widgetGroup, $widgetGrid);
        $widgetAdd = array_values($widgetAdd);
        $widgets = \Crm\Models\Widgets::find([['widgetId' => ['$in' => $widgetAdd]]]);
        $widgetAdd = array();
        foreach ($widgets as $value) {
            $widgetAdd[] = array(
                'id' => $value->widgetId,
                'name' => $value->name,
            );
        }
        return $widgetAdd;
    }

    // Добавляет виджет на сетку пользователю (в коллекцию WidgetsCustomGridUsers)
    // имя виджета берет из $_GET - параметра
    // пользователя берет из $auth = new \Crm\Auth\Auth(); $identity = $auth->getIdentity();
    static function widgetAddGrid()
    {
        $auth = new \Crm\Auth\Auth();
        $identity = $auth->getIdentity();
        $result = array('success' => true);
        if ($identity) {
            $widgetGridOb = \Crm\Models\WidgetsCustomGridUsers::find([['user' => $identity['name']]]);
            if (isset($widgetGridOb[0])) {
                $widgetGrid = $widgetGridOb[0];
                $request = new \Phalcon\Http\Request();
                $receive = $request->getQuery();
                if (isset($receive['widgetName']) && !in_array($receive['widgetName'],
                        array_merge($widgetGrid->grid[1], $widgetGrid->grid[2]))
                ) {

                    array_unshift($widgetGrid->grid[1], $receive['widgetName']);
                    if (!$widgetGrid->save()) {
                        $result = array('success' => false);
                    }
                } else {
                    $result = array('success' => false);
                }
            } else {
                $result = array('success' => false);
            }
        } else {
            $result = array('success' => false, 'reason' => 'Unauthorized');
        }
        if ($result['success']) {
            $widget = self::widgetsForGrid(array($receive['widgetName']))[$receive['widgetName']];
            $result['widget'] = $widget;
        }
        return $result;
    }

    //Возвращает подготовленный массив с контентом виджетов для вставки в сетку на дашбоарде
    //принимает список виджетов, например: array("Sales", "Profitability")
    //
    static function widgetsForGrid ($widgetsListId = array())
    {
        if ($widgetsListId != array()) {
            $unpreparatedWidgets = \Crm\Models\Widgets::find([['widgetId' => ['$in' => $widgetsListId]]]);
            $widgets = array();
            foreach ($unpreparatedWidgets as $widgetConstructorData) {// конструируем виджетики
                // вот это, переделать
                if (class_exists ('\\Crm\\Widget\\'.$widgetConstructorData->widgetFactoryType)) {
                    if($widgetConstructorData->paramsWidget)
                    {
                        $widget = \Crm\Widget\WidgetProvider::getWidget($widgetConstructorData->widgetId,
                            $widgetConstructorData->widgetFactoryType,
                            $widgetConstructorData->paramsWidget);
                    } else {
                        $widget = \Crm\Widget\WidgetProvider::getWidget($widgetConstructorData->widgetId,
                            $widgetConstructorData->widgetFactoryType);
                    }
                    if(!empty($widget))
                    {
                        $widget = '<div class="sortable-item" data-id="'.$widgetConstructorData->widgetId.'">'
                            .$widget.
                            '</div>';
                        $widgets[$widgetConstructorData->widgetId] = $widget;
                    }
                }
            }
            $result = $widgets;
        } else {
            $result = array();
        }
        return $result;
    }

    //Возвращает сетку для пользователя
    static function widgetsGridForUser()
    {
        $auth = new \Crm\Auth\Auth();
        $profile = $auth->getProfile();
        $user = $auth->getName();
        $data = \Crm\Models\WidgetsCustomGridUsers::find([['user' => $user]]);
        if (count($data) > 1) {
            throw new \Exception('Database integrity violation. Check WidgetsCustomGridUsers collection.');
        }
        $positions = $data; //[0];

        if (empty($positions)) {//coping default grid for user
            $data = \Crm\Models\WidgetsDefaultGridGroups::find([['group' => $profile]]);
            if (count($data) > 1 || empty($data) || !is_array($data) || !array_key_exists(0, $data)) {
                throw new \Exception('Database integrity violation. Check WidgetsDefaultGridGroups collection.');
            }
            $document = $data[0];
            unset($data);
            $positions = $document;


            if (empty($positions) && !empty($widgets)) { // виджеты без сетки. Это, че- т оне то
                throw new \Exception('Default widgets grid is empty. Please, contact to administrator of the system.');
            }

            $widgetsCustomGridUsers = new \Crm\Models\WidgetsCustomGridUsers();

            $widgetsCustomGridUsers->user = $user;
            $widgetsCustomGridUsers->grid = $positions->grid;

            if (!$widgetsCustomGridUsers->save()) {
                throw new \Exception('Error while updating user\'s widgets grid');
            }

            $positions = \Crm\Models\WidgetsCustomGridUsers::find([['user' => $user]]);
        }

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
            foreach ($grid[1] as $key => $val) {
                if (!class_exists ('\\Crm\\Widget\\'.$val) or !WidgetProvider::roleHasAccess($val)) {
                    unset($grid[1][$key]);
                }
            }
            foreach ($grid[2] as $key => $val) {
                if (!class_exists ('\\Crm\\Widget\\'.$val) or !WidgetProvider::roleHasAccess($val)) {
                    unset($grid[2][$key]);
                }
            }
        }
        return $grid;
    }

    //Удаление виджета из сетки пользователя
    static function widgetRemove()
    {
        $auth = new \Crm\Auth\Auth();
        $identity = $auth->getIdentity();
        $result = array('success' => false);
        $request = new \Phalcon\Http\Request();
        $receive = $request->getQuery();

        if (isset($receive['widgetName'])) {
            $widgetName = $receive['widgetName'];
        } else {
            return $result;
        }

        if ($identity) {
            $widgetGridOb = \Crm\Models\WidgetsCustomGridUsers::findFirst([['user' => $identity['name']]]);
            if ($widgetGridOb) {
                if (isset($widgetGridOb->grid) && is_array($widgetGridOb->grid)) {
                    foreach ($widgetGridOb->grid as $key1 => $column) {
                        if (is_array($column)) {
                            foreach ($column as $key2 => $val) {
                                if ($widgetName == $val) {
                                    unset($widgetGridOb->grid[$key1][$key2]);
                                    if ($widgetGridOb->save()) {
                                        $result = array('success' => true);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $result = array('success' => false, 'reason' => 'Unauthorized');
        }
        return $result;
    }
}