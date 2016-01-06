<?php
/**
 * Created by PhpStorm.
 * User: kostja
 * Date: 11.06.15
 * Time: 17:51
 */

namespace Crm\EventsModel;


class Log {
    static function push($event, $model)
    {
        //@todo add save Db collection
        $eventType = $event->getType();
        $class = get_class($model);
        return true;
    }
}