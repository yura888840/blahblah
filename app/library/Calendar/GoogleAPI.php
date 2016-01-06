<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 25.03.15
 * Time: 12:14
 */

namespace Crm\Calendar;

use Phalcon\Mvc\User\Component;

/**
 * Class GoogleAPI working with google API
 * @package Crm\Calendar
 */
class GoogleAPI extends Component
{

    /**
     * Returns service calendar 
     *
     * @return object Google_Service_Calendar
     */
    public function getService()
    {
        $clientId = $this->config->api->google->calendar->client_id;
        $emailService = $this->config->api->google->calendar->service_email;
        $keyFile = $this->config->api->google->calendar->service_key_p12_path;
        $key = file_get_contents($keyFile);
        $client = new \Google_Client();
        $client->setApplicationName($this->config->api->google->calendar->app_name);
        $client->setClientId($clientId);
        $client -> setAssertionCredentials ( new  \Google_Auth_AssertionCredentials (
            $emailService ,
            array ( 'https://www.googleapis.com/auth/calendar' ),
            $key ));
        $serviceCalendar = new \Google_Service_Calendar($client);
//        $serviceCalendar->events->update();
        return $serviceCalendar;
    }

    public function sendTicketsToGoogleCalendar()
    {
        $tickets = \Crm\Models\Tickets::find([]);
        foreach ($tickets as $ticket) {
            if (!empty($ticket->deadline)) {
                $this->addEventTicket($ticket);
            }
        }
        return true;
    }

    public function addEventTicket($ticket)
    {
        if ($ticket->deadline->sec < $ticket->created->sec) {
            return true;
        }
        $event = new \Google_Service_Calendar_Event();
        $event->setSummary($ticket->subject);
        $event->setDescription(' <a href="'.$this->config->application->baseUri.'tickets/viewTicket/'.$ticket->_id.'">Go ticket.</a> '.$ticket->description);

        $start = new \Google_Service_Calendar_EventDateTime();
        $date1 = $ticket->created->sec;
        $start->setTimeZone('UTC');
        $start->setDateTime(date('Y-m-d', $date1).'T'.date('H:i:s', $date1));
        $event->setStart($start);

        if (!empty($ticket->deadline->sec)) {
            $date2 = $ticket->deadline->sec;
        } else {
            $date2 = strtotime('+1 day' . date('Y-m-d H:i:s', $date1));
        }
        $end = new \Google_Service_Calendar_EventDateTime();
        $end->setTimeZone('UTC');
        $end->setDateTime(date('Y-m-d', $date2).'T'.date('H:i:s', $date2));
        $event->setEnd($end);

        $serviceCalendar = $this->getService();

        $updated = false;
        if (isset($ticket->google_calendar)){
            foreach ($ticket->google_calendar as $calendar) {
                if ($calendar['id_calendar'] == $this->getCrmCalendarId()) {
                    //update event in calendar
                    $serviceCalendar->events->update($this->getCrmCalendarId(), $calendar['id_event'], $event);
                    $updated = true;
                    break;
                }
            }
        }
        if (!$updated){
            //insert event in calendar
            $createdEvent = $serviceCalendar->events->insert($this->getCrmCalendarId(), $event);
            $google_calendar = array();
            $google_calendar['id_calendar']=$this->getCrmCalendarId();
            $google_calendar['id_event']=$createdEvent->getId();
            $ticket->google_calendar[] = $google_calendar;
            $ticket->save();
        }
//        $id = $createdEvent->getId();
        return true;
    }

    public function getCrmCalendarId()
    {
        return $this->config->api->google->calendar->crm_calendar_id;
    }

    public function getClient(){
        $urlCalendar = $this->config->application->baseUri.$this->config->api->google->calendar->redirect_uri;
        $clientId = $this->config->api->google->calendar->client_id;
        $secret = $this->config->api->google->calendar->secret;
        $app_name = $this->config->api->google->calendar->app_name;

        $client = new \Google_Client();
        $client->setApplicationName($app_name);
        $client->setClientId($clientId);
        $client->setClientSecret($secret);
        $client->setRedirectUri($urlCalendar);
        $client->setAccessType('online');   // Gets us our refreshtoken
        $client->setScopes(array('https://www.googleapis.com/auth/calendar.readonly'));
        return $client;
    }

    public function listCalendarList(){
        try {
            $client = $this->getClient();
            $token = $this->session->get("tokenGoogleCalendar");
            $client->setAccessToken($token);
            $service = new \Google_Service_Calendar($client);
            $calendarList  = $service->calendarList->listCalendarList();
            return $calendarList;
        } catch (\Exception $e) {
            $calendarList = false;
            return $calendarList;
        }
    }

    public function listEvents($calendarId){
        try {
            $client = $this->getClient();
            $token = $this->session->get("tokenGoogleCalendar");
            $client->setAccessToken($token);
            $service = new \Google_Service_Calendar($client);
            $events  = $service->events->listEvents($calendarId);
            return $events;
        } catch (\Exception $e) {
            $events = false;
            return $events;
        }
    }

    public function setToken($client) {
        $code = $this->request->getQuery("code");
        if ($code) {
            $client->authenticate($code);
            $token = $client->getAccessToken();
            $this->session->set("tokenGoogleCalendar", $token);
            return $token;
        }
        return true;
    }



} 