<?php
/**
 * Created by PhpStorm.
 * User: Kostja
 * Date: 22.07.15
 * Time: 11:01
 */

namespace Crm\Events;


use Crm\Models\Tickets;

class Log
{

    // обработчик событий - validation, Save, Update

    static function save($event, $model)
    {
        $modelName = get_class($model);
        $eventType = $event->getType();
        //logging tickets events
        if (($eventType == 'beforeSave' || $eventType == 'afterSave') && ($modelName == 'Crm\Models\Tickets' or $modelName == 'Crm\Models\Comments')) {
            $ticketEvents = new \Crm\Models\Services\TicketsEvents();
            $ticketEvents->run($event, $model);
        }

        //END logging tickets events
        return true;
    }

}