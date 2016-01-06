<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 27.02.15
 * Time: 15:29
 */

namespace Crm\Models;

use Crm\Helpers\DataSets;

//@todo подчистить. Вынести ticket workflow - отдельно
class Tickets extends CollectionBase
{

    protected static $isGuestAllowed = true;

    protected static $isUserAllowed = true;

    protected static $isConsoleAllowed = true;

    public $_id;

    public $priority = 'Normal';

    public $status = 'New';

    public $type;

    public $name;

    public $department;

    public $subject;

    public $pageUrl;

    public $description;

    public $created;

    public $authorId = '';

    public $authorName = '';

    public  $myPosition;

    // deprecated
    public $usersId;

    public $projectsId;

    public $userName;

    public $userEmail;

    public $notify = [];
    
    public $attach = [];

    public $assignTo;

    public $reportedBy;

    public $isNew;

    public $isFromEmail = false;

    public $messageId;

    public $fromEmail;

    public $deadline = '';
    

    /**
     * TRIGGERS Block
     */
    public function beforeSave()
    {
        if(empty($this->created)) {
            $this->created = new \MongoDate();
        }

        if($this->isNew)
        {
            //hack
            $this->assignTo = $this->name;

            if(empty($this->assignTo))
            {
                $this->status = 'Unassigned';
            } else {
                $this->status = 'New';
            }

            $identity = $this->getDI()->get('auth')->getIdentity();

            $this->reportedBy = $identity;

            $this->isNew = false;
        }
        if ($this->status == 'Closed'){
            $date1 = date_create(date('Y-M-d', $this->created->sec));
            $date2 = date_create(date('Y-M-d'));
            $interval = date_diff($date1, $date2);
            $interval = (int)$interval->format('%R%a');
            $this->closed_interval = $interval;
            $this->closed = new \MongoDate();
        } else {
            $this->closed = null;
            $this->closed_interval = null;
        }

        settype($this->priority, 'int');
    }


    protected static function beforeFind($parameters)
    {
        return parent::beforeFind($parameters);
    }

    protected static function afterFind($result)
    {
        // result postprocess

        return parent::afterFind($result);
    }

    /**
     * !END of - TRIGGERS block
     */

    /**
     * Это какие- то права доступа к тикету
     *
     * @return string
     */
    public function editRoleRights(){
        return 'admin';
    }

    /**
     * Логика тикета (workflow )
     *
     * @param $ticket
     * @param $authData
     * @return string
     */
    public static function ticketWorkFlowButtons($ticket, $authData){
        $url = 'tickets/editOneField';
        $workFlowButtons = [];
        $workFlowButtonsString = '';

        if($ticket -> assignTo && $authData['id'] == $ticket -> assignTo){
            if($ticket -> status == 'Assigned' || $ticket -> status == 'Re-Assigned'){
                $workFlowButtons[] = 'In-Progress';
            }
            elseif($ticket -> status == 'In-Progress' || $ticket -> status == 'To-Rework'){
                $workFlowButtons[] = 'Resolved';
            }
        }

        $editRole = $ticket -> editRoleRights();

        if($ticket -> status == 'Resolved' && $authData['profile'] ==  $editRole){
            $workFlowButtons[] = 'To-Rework';
        }
        if($ticket -> status != 'Closed' && $authData['profile'] ==  $editRole) {
            $workFlowButtons[] = 'Closed';
        }
        elseif($ticket -> status == 'Closed' && $authData['profile'] ==  $editRole){
            $workFlowButtons[] = 'Re-Opened';
        }

        foreach($workFlowButtons as $button){

            $workFlowButtonsString .=
                '<a class="btn btn-default add workFlowTicketButtons" work-flow-status ="'
                . $button
                . '" href = "' . $url . '" work-flow-id = "' . $ticket->_id . '">' . $button . '</a>';
        }

        return $workFlowButtonsString;
    }

    public function alias($fieldName)
    {
        $fieldsAlias = array(
            "status" => "status",
            "priority" => "priority",
            "type" => "type",
            "department" => "department",
            "assignTo" => "assignTo",
            "deadline" => "deadline",
            "notify" => "watchers",
            "attach" => "attach"
        );
        if ( array_key_exists($fieldName, $fieldsAlias)) {
            return $fieldsAlias[$fieldName];
        } else {
            return $fieldName;
        }
    }

}