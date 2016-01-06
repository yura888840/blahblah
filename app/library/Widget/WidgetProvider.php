<?php

namespace Crm\Widget;

use Crm\Models\WidgetsPermissions;

class WidgetProvider
{

    static function getWidget($widgetName, $widgetType, $param = array())
    {   //@todo fix this widgetType
        if (empty($widgetName) || empty($widgetType)) {
            return '';
        }

        $o = new \Crm\Widget\CrmWidgets('widgets');

        $o->checkAndInstallNew();


        $widgetClass = 'Crm\Widget\\' . $widgetType;

        $ce = $widgetClass;
        $ra = self::roleHasAccess($widgetName);

        if (class_exists($widgetClass) && self::roleHasAccess($widgetName)) {
            $widget = new $widgetClass();
            $widgetContent = $widget->run($param);

            return $widgetContent;
        }

        return '';

    }

    static function roleHasAccess($widgetClass)
    {
        $perms = WidgetsPermissions::findFirst([['name' => $widgetClass]]);

        $authData = \Phalcon\DI::getDefault()->get('auth')->getIdentity();

        if (!$perms || empty($authData)) {
            return false;
        }

        $role = $authData['profile'];

        if ($perms->permissions && $perms->permissions[$role]) {
            return true;
        }
        //@todo check access for user name

        return false;

    }

}