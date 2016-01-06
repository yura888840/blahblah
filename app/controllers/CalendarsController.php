<?php
/**
 * Author: eugene
 * Date: 09.07.15
 */

class CalendarsController extends ControllerBase {

    public function getAjaxEventsAction()
    {
        $this->view->disable();
        $request = new \Phalcon\Http\Request();
        $query = $request->getQuery();
        $dateStart = $query['dateStart']; //начальная дата
        $dateEnd = $query['dateEnd']; //конечная дата
        $userName = $_SESSION["auth-identity"]["name"]; //имя пользователя (если выборка для пользователя, а не для диапазона дат)
//Массив $colors и функция getColor являются внутренними и больше нигде не используются. Кроме того, они еше и временные,
// т.е. это заплатка", пока не реализовано задания цвета пользователя либо из формы, либо им автоматически в момент создания нового пользователя...
// их надо будет убрать.
        global $colors; $colors=[ // цвета скопированы у google Calendar
            "#ac725e","#d06b64","#f83a22","#fa573c","#ff7537","#ffad46"
            ,"#42d692","#16a765","#7bd148","#b3dc6c","#fbe983","#fad165"
            ,"#92e1c0","#9fe1e7","#9fc6e7","#4986e7","#9a9cff","#b99aff"
            ,"#c2c2c2","#cabdbf","#cca6ac","#f691b2","#cd74e6","#a47ae2"
        ];
        function getColor() {
            global $colors;
            array_push($colors,array_shift($colors)); // первый элемент в конец массива. Т.е. циклически перебираем цвета#
            return $colors[0];
        }

        $identity = $this->auth->getIdentity();
        $userId = (string)$identity['id'];
        $findCriteria = array();
        $typeCalendar = $request->getQuery('typeCalendar');
        if ($typeCalendar == 'personal') {
            $findCriteria["assignTo"] = $userId;
        }
        if (!$identity['see_company_calendar']) {
            $findCriteria["assignTo"] = $userId;
        }

// Сейчас используется два варианта выборки: 1. тикеты за заданный диапазон, 2. все тикеты текущего пользователя.
        if (isset($dateStart)) { // начальная дата задана - тикеты за заданный диапазон
            $sdat = date_parse($dateStart);
            $sdat = new MongoDate(mktime(0, 0, 0, $sdat['month'], $sdat['day'], $sdat['year'])); // в формат MongoDB
            if (!isset($dateEnd)) $dateEnd = $dateStart; // начальная дата задана, а конечная нет, приравниваем их, т.е. за один день == начальной дате
            $edat = date_parse($dateEnd);
            $edat = new MongoDate(mktime(0, 0, 0, $edat['month'], $edat['day'], $edat['year']));

            $findCriteria['created'] = ['$lt'=>$edat];
            $findCriteria['$or']=[
                ['deadline'=>['$eq' => NULL]], // if deadline is NULL (theoretically)
                ['deadline'=>['$gte'=>$sdat]]
            ];
        }
        else {//начальная дата не задана - делаем выборку всех тикетов текущего пользователя.
            $findCriteria=["assignTo" => $userId];
        }
//////////// конец фрагмента блока выборок по запросам /////////////
        $assignToIDs = $tickets = [];
        $tickets = \Crm\Models\Tickets::find([$findCriteria,"fields"=>['subject'=>1,'description'=>1,'id'=>1,'name'=>1,'created'=>1,'deadline'=>1,'assignTo'=>1,'updated_at'=>1,'created_at'=>1]]);
        foreach ($tickets as $ticket) {
            if (!isset($assignToIDs[$ticket->assignTo])) $assignToIDs[$ticket->assignTo]=new MongoId($ticket->assignTo);
            $rec=[
                'title' => $ticket->subject,
                'description' => $ticket->description,
                'id' => (string)$ticket->_id,
                'name' => $ticket->name,
                'assignTo' => $ticket->assignTo,
                'start' => date(DATE_RFC3339, $ticket->created->sec), //было DATE_ISO8601 (без : у времени)
                'updated_at' => date(DATE_RFC3339, $ticket->updated_at->sec),
                'created_at' => date(DATE_RFC3339, $ticket->created_at->sec),
            ];
            if (!empty($ticket->deadline)) {
                $rec['end'] = date(DATE_RFC3339, $ticket->deadline->sec);
            }
            $events[]=$rec;
        }
        if (count($tickets)==0) {
            $res = array();
            $res['events'] = [];
            $res['see_company_calendar'] = $identity['see_company_calendar'];
            echo json_encode($res);
            return;
        }

        $findCriteria=['$or'=>[]];

        foreach ($assignToIDs as $userId) {
            $findCriteria['$or'][]=['_id'=>['$eq' => $userId]];
        }
        $usersCol = \Crm\Models\Users::find([$findCriteria,"fields"=>['id'=>1,'name'=>1,'color_ticket'=>1]]);
        $users=[];
        foreach ($usersCol as $user) {$userId=(string)$user->_id;
            if (!isset($users[$userId])) {
                if (!isset($user->color_ticket)) { //Если color_ticket не задан в коллекции - устанавливаем и сохраняем
                    $usr=\Crm\Models\Users::findById($userId);
                        $usr->color_ticket = $user->color_ticket = getColor();
                        $usr->save();
                }
                $users[$userId]=['name'=>$user->name,'color_ticket'=>$user->color_ticket];
            }
        }

        for ($i=0;$i<count($events);$i++) {$userId=$events[$i]['assignTo']; //$idas=$event['assignTo'];
            $events[$i]['assignTo']=$users[$userId]['name'];
            $events[$i]['color']=$users[$userId]['color_ticket'];
        }
        $res = array();
        $res['events'] = $events;
        $res['see_company_calendar'] = $identity['see_company_calendar'];
        echo json_encode($res);
//        return $events;
    }
//*******************************************************************************
    public function indexAction()
    {
        $calendarApi = new \Crm\Calendar\GoogleAPI();
        $client = $calendarApi->getClient();
        $this->view->setVar('authUrl', $client->createAuthUrl());
        $clientId = $this->config->api->google->calendar->client_id;
        $apiKey = $this->config->api->google->api_key;
        $this->view->setVar('clientId', $clientId);
        $this->view->setVar('apiKey', $apiKey);
    }

    public function redirectGoogleAction()
    {
        $calendarApi = new \Crm\Calendar\GoogleAPI();
        $client = $calendarApi->getClient();
        $calendarApi->setToken($client);
        $this->response->redirect('calendars');
    }

    public function ajaxExportEvensAction()
    {
        $this->view->disable();

//        $this->session->remove("tokenGoogleCalendar");

        $calendarApi = new \Crm\Calendar\GoogleAPI();
        $calendarList = $calendarApi->listCalendarList();
        if ($calendarList) {
            echo 'ok connect';
        } else {
            echo 'error connect';
        }
    }

    private function getDataForEvents()
    {
        $events = [];

        $tickets = \Crm\Models\Tickets::find([]);

        foreach ($tickets as $ticket) {
            if (!empty($ticket->deadline)) {
                $events[] = [
                    'title' => $ticket->subject,
                    'id' => $ticket->_id
                ];
            }
        }

        return $events;

    }

    public function restoreEventAction()
    {
        $request = new \Phalcon\Http\Request();
        $idEvent = $request->getPost("id");
        $res = \Crm\Helpers\DeleteHelper::restoreFromTrash($idEvent);
        echo json_encode($res);
    }

    public function changeGoogleCalendarForUserAction()
    {
        $request = new \Phalcon\Http\Request();
        $res = false;
        $idGoogleCalendar = $request->getPost("crm_gcal_id");
        $identity = $this->auth->getIdentity();
        $userId = (string)$identity['id'];
        $user = \Crm\Models\Users::findById($userId);
        if ($user) {
            $user->google_calendar_id = $idGoogleCalendar;
            if ($user->save()) {
                $res = true;
            }
        }
        echo json_encode($res);
    }

}