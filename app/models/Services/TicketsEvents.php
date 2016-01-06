<?php
/**
 * Created by PhpStorm.
 * User: Kostja
 * Date: 22.07.15
 * Time: 11:08
 */

namespace Crm\Models\Services;


class TicketsEvents extends \Phalcon\Mvc\Collection
{
    static $ticketOld;
    static $commentOld;

    private $fieldsShowDetail = array(
        "status",
        "priority",
        "type",
        "department",
        "deadline",
    );

    public function run($event, $model)
    {
        $eventType = $event->getType();
        $modelName = get_class($model);

        if ($modelName == 'Crm\Models\Tickets') {
            if ($eventType == 'beforeSave' && isset($model->_id)) {
                self::$ticketOld = \Crm\Models\Tickets::findById((string)$model->getId());
            }
            if ($eventType == 'afterSave') {
                $this->addEventTicket(self::$ticketOld, $model);
                self::$ticketOld = null;
            }
        }

        if ($modelName == 'Crm\Models\Comments') {
            if ($eventType == 'beforeSave' && isset($model->_id)) {
                self::$commentOld = \Crm\Models\Comments::findById((string)$model->getId());
            }
            if ($eventType == 'afterSave') {
                $this->addEventComment(self::$commentOld, $model);
                self::$commentOld = null;
            }
        }

        return true;
    }

    public function addEventTicket($ticketOld, $ticketNew)
    {
        $identity = \Phalcon\DI::getDefault()->get('auth')->getIdentity();
        $userName = (is_array($identity) && array_key_exists('name', $identity)) ? $identity['name'] : '';
        $description = '';
        if ($ticketOld) {
            foreach ($ticketOld as $key => $value) {
                if ( substr($key, 0, 1) != '_' ) {
                    if ( !isset($ticketNew->$key) || (isset($ticketNew->$key) && $value != $ticketNew->$key) ) {
                        if ($ticketOld->$key===null and $ticketNew->$key===null) {
                            continue;
                        }
                        if ( isset($ticketNew->$key) ) {//update field
                            $pre = ($description=='') ? '' : '. ';
                            $description .= $pre.'Update '.$ticketNew->alias($key);
                            if ( in_array($key, $this->fieldsShowDetail) ) {
                                $value2 = $ticketNew->$key;
                                if ($ticketNew->alias($key)=='deadline') {
                                    $value = date('Y-m-d',$value->sec);
                                    $value2 = date('Y-m-d',$value2->sec);
                                }
                                $description .= ': '.$value.' => '.$value2;
                            }
                        } else {//add new field
                            $pre = ($description=='') ? '' : '. ';
                            $description .= $pre.'Add '.$ticketNew->alias($key);
                        }
                    }
                }
            }
        } else {
            $description = 'Add new ticket';
        }

        $description = ($description=='') ? '' : $description.'.';
        if ($description > '') {
            $this->created_at = new \MongoDate();
            $this->ticket_id = $ticketNew->getId();
            $this->user_id = $identity['id'];
            $this->user_name = $userName;
            $this->description = $description;
            $this->save();
        }
    }

    public function addEventComment($commentOld, $commentNew)
    {
        $identity = \Phalcon\DI::getDefault()->get('auth')->getIdentity();
        $userName = (is_array($identity) && array_key_exists('name', $identity)) ? $identity['name'] : '';
        if ($commentOld) {
            $description = 'Update comment';
        } else {
            $description = 'Add comment';
        }

        $description = ($description=='') ? '' : $description.'.';
        if ($description > '') {
            $this->created_at = new \MongoDate();
            $this->ticket_id = $commentNew->parent_id;
            $this->comment_id = $commentNew->getId();
            $this->user_id = $identity['id'];
            $this->user_name = $userName;
            $this->description = $description;
            $this->save();
        }
    }
}