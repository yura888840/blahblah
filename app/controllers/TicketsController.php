<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 19.05.15
 * Time: 13:03
 */

use Crm\Forms\FormFactory;
use Crm\Models\Tickets;
use Phalcon\Filter;
use Phalcon\Validation\Validator\InclusionIn;
use \Crm\Acl\TicketsAcl;
use \Crm\Helpers\TicketsHelper;

class TicketsController extends ControllerBase
{
    public function indexAction()
    {
        $ticketsList = \Crm\Models\Tickets::find([]);

        $ticketsList = \Crm\Helpers\TicketsHelper::prepareTicketsList($ticketsList);
        $this->view->setVar('ticketslist', $ticketsList);

        $newCount = \Crm\Helpers\TicketsHelper::countNew();
        $this->view->setVar('count_new', $newCount);

        $ticket = new Tickets();
        $ticketForm = new \Crm\Forms\TicketEditForm($ticket);
        $this->view->form = $ticketForm;

        $this->view->setVar('department', \Crm\Helpers\DataSets::getSetByName('department'));
        $this->view->setVar('status', \Crm\Helpers\DataSets::getSetByName('status'));
        $this->view->setVar('type', \Crm\Helpers\DataSets::getSetByName('type'));
        $this->view->setVar('priority', \Crm\Helpers\DataSets::getSetByName('priority'));
    }

    /**
     * Просмотр тикета вместе с + логика на доступ к тикету
     *
     * @return mixed
     */
    public function viewTicketAction()
    {

        $ticketID = $this->dispatcher->getParam(0);
        $authData = $this->auth->getIdentity();

        $ticket = Tickets::findById($ticketID);

        if (!$ticket) {
            // fix. Костыль
            return $this->response->redirect('tickets/viewEmail/' . $ticketID);
        }

        $access = TicketsAcl::hasAccess($ticket);

        if (!$access) {
            $response = new \Phalcon\Http\Response();
            return $response->redirect('/tickets/personal');
        }

        $ticket->userAbbrev = substr(ucfirst($ticket->name), 0, 1);

        $this->view->workFlowButtons = Tickets::ticketWorkFlowButtons($ticket, $authData);
        $this->view->editRight = ($authData['profile'] == $ticket->editRoleRights()) ? true : false;

        //TicketsHelper::fixMissingAttachments($ticket);

        $ticket->created = date('Y-m-d H:i:s',
            is_object($ticket->created)
                ? $ticket->created->sec
                : 0
        );

        if (!empty($ticket->deadline)) {
            $ticket->deadline = \Crm\Helpers\DateTimeFormatter::reverseDeadlineFormatter($ticket->deadline);
        }

        //hack
        $assignToData = \Crm\Models\Users::findById($ticket->assignTo);
        // Notice: Trying to get property of non-object in /var/www/crm/app/controllers/TicketsController.php on line 82
        // Несоблюдение граничн условий
        $ticket->assignTo = (string)$assignToData->_id;

        $ticketForm = FormFactory::getForm('TicketEditForm', $ticket);

        if (!$ticketForm) {
            $this->flash->error('You don\'t have access to this module');
            return $this->response->redirect('dashboard');
        }

        $opts = TicketsHelper::getOptions($ticket, $ticketForm);

        foreach ($opts['varsForView'] as $var => $val) {
            $this->view->$var = $val;
        }

        $this->view->x_options = $opts['elOptions'];

        $this->view->ticket = new ArrayObject($ticket->toArray());

        $form = FormFactory::getForm('TicketCommentsForm');

        if ($form) {
            $this->view->form = $form;
        }

        $this->view->comments = new ArrayObject(\Crm\Helpers\CommentsHelper::commentsView($ticketID));
        $newCount = \Crm\Helpers\TicketsHelper::countNew();

        $this->view->setVar('count_new', $newCount);


        $customForm = TicketsHelper::buildTicketSettingsForm($ticket);

        $this->forms->set('customform', $customForm['form']);

        $this->view->setVar('formname', 'customform');
        $this->view->setVar('formfieldlist', $customForm['formFieldlist']);
        $this->view->setVar('templates', $customForm['templates']);


        $replyonTicketForm = TicketsHelper::buildReplyOnTicketSubform($ticket);

        $this->forms->set('replyonticketform', $replyonTicketForm['form']);

        $this->view->setVar('formname1', 'replyonticketform');
        $this->view->setVar('form1fieldlist', $replyonTicketForm['formFieldlist']);
        $this->view->setVar('templates1', $replyonTicketForm['templates']);


        if (!empty($ticket->deadline)) {
            $this->view->setVar('deadline', $ticket->deadline);
        }

        // @todo refactor this
        $ticket1 = new Tickets();
        $ticketForm = new \Crm\Forms\TicketEditForm($ticket1);
        $this->forms->set('add', $ticketForm);

        $this->view->setVar('needAnimate', false);


        // comments attachments

        $attaches = [
            ['commentId' => '5510250b51be0bf00d8b4569_1d',
                'uniqName' => '5510250b51be0bf00d8b4569/main/59c62a80fd071b6ba3d4c830ea4d0d6ec7af14da.pdf',
                'originalName' => 'Increasing_AOV_Final.pdf',
                'comment_header' => 'Kanban book ..',
                'comment_full' => substr('Kanban book in attachment', 0, 30),
            ],

            ['commentId' => '5510250b51be0bf00d8b4569_2d',
                'uniqName' => '5510250b51be0bf00d8b4569/main/59c62a80fd071b6ba3d4c830ea4d0d6ec7af14da.pdf',
                'originalName' => 'Ajax form.docx',
                'comment_header' => 'Ajax form ..',
                'comment_full' => substr('Ajax form description', 0, 30),
            ],

        ];

        // Заглушка. ПоФиксить
        $attaches = [];

        $this->view->setVar('comments_attachments', $attaches);

    }

    protected function setCommonVars()
    {

    }

    private function emailToCommentMapper($emails)
    {
        $mapping = [
            '_id' => function ($in) {
                $out = $in->_id;
                return $out;
            },
            'parent_id' => function ($in) {
                $out = null;
                return $out;
            },
            'parent_name' => function ($in) {
                $out = 'email';
                return $out;
            },
            'user_id' => function ($in) {
                $out = null;
                return $out;
            },
            'user_name' => function ($in) {//****
                if ($in->isInSent) {
                    $out = '';
                } else {
                    $out = $in->fromAddress;
                }
                return $out;
            },
            'text' => function ($in) {
                $out = $in->body;
                return $out;
            },
            'attach' => function ($in) {
                $out = [];
                return $out;
            },
            'isPrivate' => function ($in) { //****
                $out = $in->isInSent;
                return $out;
            },
            'notified' => function ($in) {
                $out = [];
                return $out;
            },
            'created' => function ($in) {
                $out = \Crm\Helpers\DateTimeFormatter::format($in->created);
                $out = $out['created'];
                return $out;
            },
            'created_date' => function ($in) {
                $out = \Crm\Helpers\DateTimeFormatter::format($in->created);
                $out = $out['created_date'];
                return $out;
            },
            'created_time' => function ($in) {
                $out = \Crm\Helpers\DateTimeFormatter::format($in->created);
                $out = $out['created_time'];
                return $out;
            },
            'created_date_time' => function ($in) {
                $out = \Crm\Helpers\DateTimeFormatter::format($in->created);
                $out = $out['created_date_time'];
                return $out;
            },
            'created_date_text' => function ($in) {
                $out = \Crm\Helpers\DateTimeFormatter::format($in->created);
                $out = $out['created_date_text'];
                return $out;
            },
            'created_date1_text' => function ($in) {
                $out = \Crm\Helpers\DateTimeFormatter::format($in->created);
                $out = $out['created_date1_text'];
                return $out;
            },
            'recepient_name' => function ($in) { //*****
                if ($in->isInSent) {
                    $out = '';
                } else {
                    $out = $in->toAddress;
                }
                return $out;
            },
            'recepient_email' => function ($in) { //*****
                if ($in->isInSent) {
                    $out = '';
                } else {
                    $out = $in->toAddress;
                }
                return $out;
            },
            'userAbbrev' => function ($in) { // ****
                if ($in->isInSent) {
                    $out = '';
                } else {
                    $out = ucfirst($in->fromAddress);
                }
                return $out;
            },
            'userFullname' => function ($in) { //*****
                if ($in->isInSent) {
                    $out = '';
                } else {
                    $out = $in->fromAddress;
                }
                return $out;
            },
            'userEmail' => function ($in) {//*****
                if ($in->isInSent) {
                    $out = '';
                } else {
                    $out = $in->fromAddress;
                }
                return $out;
            },
        ];

        return \Crm\Helpers\EmailsHelper::mapper($emails, $mapping, false);
    }

    public function viewEmailAction()
    {
        $emailID = $this->dispatcher->getParam(0);

        $email = \Crm\Models\Email::findById($emailID);

        if (!$email)
            return $this->response->redirect('tickets');

        $email = $email->toArray();
        $parentId = $email['messageId'];
        array_walk($email, function (&$v, $k) use (&$email) {
            if ($k !== 'body') $email[$k] = htmlspecialchars($v);
            if ($k == '_id') $email[$k] = strval($v);
        });

        $email['userAbbrev'] = ucfirst(substr($email['fromAddress'], 0, 1));
        $this->view->setVar('email', $email);
        $this->view->setVar('ticket', $email);

        $emailThread = \Crm\Helpers\EmailsHelper::getThread($parentId);
        $mapped = \Crm\Helpers\EmailsHelper::mapChildrenEmails($emailThread);

        $mapped = $this->emailToCommentMapper($mapped);

        $this->view->comments = new ArrayObject($mapped);


        // Это какая- то хрень. Какой- то, фаст- фикс
        $ticket = new \Crm\Models\Tickets;
        $replyonTicketForm = TicketsHelper::buildReplyOnTicketSubform($ticket);


        $ticketTemp = $ticket->toArray();
        $ticketTemp = array_merge($email, $ticket);

        $this->view->setVar('ticket', $ticketTemp);
        $this->forms->set('replyonticketform', $replyonTicketForm['form']);
        $this->view->setVar('formname1', 'replyonticketform');
        $this->view->setVar('form1fieldlist', $replyonTicketForm['formFieldlist']);
        $this->view->setVar('templates1', $replyonTicketForm['templates']);

        $ticket1 = new Tickets();
        $ticketForm = new \Crm\Forms\TicketEditForm($ticket1);
        $this->forms->set('add', $ticketForm);

        $this->view->setVar('needAnimate', false);

    }

    public function personalAction()
    {

    }

}