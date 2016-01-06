<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 15.04.15
 * Time: 18:27
 */

namespace Crm\Helpers;

use Crm\Models\Widgets;
use Crm\Models\Permissions;

/**
 * Class WidgetsPermissionsUpdater сборщик мусора для виджетов
 * @package Crm\Helpers
 */
class WidgetsPermissionsUpdater
{

    public static function run($widgets = array())
    {
        foreach ($widgets as $wName => $wPerms) {
            $widget = Widgets::findFirst([['widgetId' => $wName]]);

            $widget->permissions = $wPerms;

            if (!$widget->save()) {
                throw new \Exception('Error while updating widget permissions');
            }
        }
    }

    public static function clean($widgets = array())
    {

    }

}