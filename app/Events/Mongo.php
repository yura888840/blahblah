<?php
/**
 * Created by PhpStorm.
 * User: Kostya
 * Date: 21.07.15
 * Time: 12:24
 */

namespace Crm\Events;


class Mongo
{
    public function manager($event, $model)
    {
        $modelName = get_class($model);
        $eventType = $event->getType();
        $security = new \Crm\Permissions\SecurityPlugin();
        $security->checkDbFieldsAccess($event, $model);
        $this->setDefaultField($event, $model);
        Log::save($event, $model);
        return true;
    }

    private function setDefaultField ($event, $model)
    {
        $eventType = $event->getType();
        $modelName = get_class($model);
        if ($modelName == 'Crm\Models\Trash') {
            $name_created_at = "trash_created_at";
            $name_updated_at = "trash_updated_at";
        } else {
            $name_created_at = "created_at";
            $name_updated_at = "updated_at";
        }

        if ($eventType == 'beforeSave') {
            if (!isset($model->created_at)) {
                $model->$name_created_at = new \MongoDate();
            }
            $model->$name_updated_at = new \MongoDate();
        }
    }
}