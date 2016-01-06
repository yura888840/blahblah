<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 30.04.15
 * Time: 15:49
 */

namespace Crm\Widget;

use Crm\Models\CollectionFactory;
use Crm\Models\WidgetsPermissions,
    Crm\Models\WidgetsDefaultSetGroups,
    Crm\Models\Widgets;

class CrmWidgets extends \Phalcon\Acl\Resource
{
    const WIDGETS_NAMESPACE = '\\Crm\\Widget\\';

    public function __construct($name, $description = NULL)
    {
        parent::__construct($name, $description);
    }

    public function checkAndInstallNew()
    {
        $dir = \Phalcon\DI::getDefault()->get('config')->application->widgetDir;

        foreach (glob($dir . '*.php') as $widget) {
            $file = substr($widget, strrpos($widget, '/') + 1);
            $className = substr($file, 0, strrpos($file, '.'));
            $classNameWthNamespace = self::WIDGETS_NAMESPACE . $className;

            $classImplements = class_implements($classNameWthNamespace);
            if (!$this->isInstalled($className)
                && class_exists($classNameWthNamespace)
                && in_array('Crm\Widget\IWidget', $classImplements)
                && in_array('Crm\Widget\IInstantiable', $classImplements)
            ) {
                $this->setupWidget($classNameWthNamespace, $className);
            }
        }

        $this->cleanupRemovedWidgets();

    }

    //@todo optimize
    private function cleanupRemovedWidgets()
    {
        $widgets = Widgets::find([[]]);

        foreach ($widgets as $w) {
            if (class_exists(self::WIDGETS_NAMESPACE . $w->widgetId)) {
                continue;
            }

            $w->delete();
        }
    }

    private function isInstalled($className)
    {
        if (Widgets::find([['widgetId' => $className]])) {
            return true;
        }

        return false;
    }

    private function setupWidget($className, $widgetId)
    {
        $widgets = Widgets::find([['widgetId' => $widgetId]]);
        if ($widgets) {
            foreach ($widgets as $oldWidget) {
                $oldWidget->delete();
            }
        }

        $class = new $className();
        $params = $class->install();

        $widgets = CollectionFactory::getNewInstanceOf('Widgets', array_merge($params, array(
            'widgetId' => $widgetId,
            'name' => $params['description'],
            'permissions' => [],
        )));
        //@todo default permissions

        if (!$widgets->save()) {
            throw new \Exception('Error while mounting widget');
        }
    }
}