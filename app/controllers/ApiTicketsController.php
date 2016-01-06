<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 27.05.15
 * Time: 16:45
 */

use Crm\Forms\TicketEditForm;
use Crm\Forms\FormFactory;
use Crm\Models\Tickets;
use Crm\Helpers\AjaxAdd as AjaxAdd;
use Crm\Helpers\AjaxTablesorter;
use Crm\Helpers\AttachHelper;
use Crm\Helpers\CommentsHelper;
use Crm\Helpers\DateTimeFormatter;
use Crm\Helpers\DeleteHelper;
use Phalcon\Tag;
use Phalcon\Filter;
use Phalcon\Validation\Validator\InclusionIn;

use \Crm\Acl\TicketsAcl;
use \Crm\Helpers\TicketsHelper;
use \Crm\Models\Users;

class ApiTicketsController extends ControllerBase
{
    /// create operation
    public function ajaxAddTicketAction()
    {
        $this->view->disable();
        $request = new \Phalcon\Http\Request();
        $model = new Tickets();
        $form = new TicketEditForm($model);

        if ($request->getPost('attachments')) {
            $attachments = explode(',', $request->getPost('attachments'));
            foreach ($attachments as $v) {
                if ($this->session->has('uploads-' . $v)) {
                    $fileData = $this->session->get('uploads-' . $v);

                    $permanentFilename = \Crm\Helpers\FilesService::moveToFileStructure($fileData['uid'] . '.' . $fileData['ext']);

                    $this->session->remove('uploads-' . $v);

                    $model->attach[] = [
                        "originalName" => $fileData["originalName"],
                        "uniqName" => $permanentFilename,
                        "type" => $fileData["type"],
                        "size" => $fileData["size"],
                        "created" => $fileData["created"],
                        'hash' => $fileData["uid"],
                    ];


                }
            }

            // @todo check attachments
        }

        if ($request->isPost() == true && $request->isAjax() == true) {

            if (!AjaxAdd::ajaxSave($model, $form, $request)) {
                $model->status = 'New';
                if ($model->assignTo) {
                    $model->status = 'Assigned';
                }
                $authData = $this->auth->getIdentity();
                $model->authorId = $authData['id'];
                $model->authorName = $authData['name'];
                $model->name = $authData['name'];

                $tmp = false;
                foreach ($model->notify as $v) {
                    if ($v == $authData['id']) {
                        $tmp = true;
                        break;
                    }
                }
                if (!$tmp) {
                    $model->notify[] = (string)$authData['id'];
                }


                if ($request->hasPost('attach')) {
                    $model->attach = AttachHelper::ajaxAttachInfo($request);
                }

                if ($model->save()) {
                    if ($request->hasPost('attach')) {
                        AttachHelper::renameSavedFiles($model, 'tickets');
                    }
                    $messagesArr['success'] = true;
                    echo json_encode($messagesArr);
                }
            }

        }
    }

    /// delete operation
    public function ajaxDeleteTicketAction()
    {
        $this->view->disable();
        $request = new \Phalcon\Http\Request();

        if ($request->isPost() == true && $request->isAjax() == true) {
            $id = $request->getPost('_id');
            $data = \Crm\Models\Tickets::findById($id);
            \Crm\Helpers\DeleteHelper::moveToTrash($data, 'Tickets');
        }
    }

    /// update
    public function editOneFieldAction()
    {
        $this->view->disable();
        $request = new \Phalcon\Http\Request();

        if ($request->isPost() == true && $request->isAjax() == true) {
            $updating_parameter = $this->request->getPost('name', 'striptags');

            $ticket_data = Tickets::findById($this->request->getPost('pk', 'striptags'));
            $ticket = new Tickets();
            foreach ($ticket_data as $k => $v) {
                if ($k != 'created') {
                    $ticket->$k = $v;
                }
            }

            // Часть кода - Workflow
            if ($updating_parameter == 'assignTo') {
                $ticket->status = 'Assigned';
                if ($ticket->assignTo && $ticket->assignTo != 'Empty') {
                    $ticket->status = 'Re-Assigned';
                }
            } elseif ($updating_parameter == 'status') {
                if ($this->request->getPost('value') == 'Re-Opened') {
                    $ticket->assignTo = 'Empty';
                }
            }
            // !--

            $ticket->$updating_parameter = $this->request->getPost('value');

            if ($ticket->save()) {
                $messagesArr = new ArrayObject($ticket->toArray());
                $messagesArr['success'] = true;
                $messagesArr['created'] = DateTimeFormatter::format($ticket->created)['created'];
                $authData = $this->auth->getIdentity();
                $messagesArr['btn-wf'] = Tickets::ticketWorkFlowButtons($ticket, $authData);
                echo json_encode($messagesArr);
            }
        }
    }

    private $mapping = [
        'watchers' => 'notify',
        'assign_to' => 'assignTo',
    ];

    public function propsaveAction()
    {
        $this->view->disable();
        $request = new \Phalcon\Http\Request();

        if ($request->isPost() == true && $request->isAjax() == true) {
            $ticketID = $this->dispatcher->getParam(0);

            if (!$ticketID) {
                throw new \Exception('Incorrect ticket object id');
            }

            $ticket = Tickets::findById($ticketID);

            if (!$ticket) {
                throw new \Exception('Ticket with specified Id doesn\'t exists');
            }

            $data = $request->getPost();

            foreach ($data as $k => $v) {
                $field = $k;
                $value = $v;

                if (array_key_exists($field, $this->mapping)) {
                    $field = $this->mapping[$field];
                }

                if ($field == 'deadline') {
                    $value = \Crm\Helpers\DateTimeFormatter::deadlineFormat($value);
                }

                //hack
                if (substr($field, 0, 2) != 'dp') {

                    if (!in_array($field, array_keys(get_object_vars($ticket)))) {
                        throw new \Exception('Property ' . $field . ' doesn\'t exists');
                    }
                } else {
                    // @todo пофиксить, выделить время, и пофиксить, такие хардкоды

                    $field = 'deadline';

                    $value = \Crm\Helpers\DateTimeFormatter::deadlineFormat($value);
                }


                //@todo $value  sanitizing
                $oldValue = $ticket->$field;

                // сохранили значение
                $this->changeStatusOptionsWithFieldSave($field, $value, $ticket);

                $dataForNotification = [
                    'objectCls' => 'tickets',
                    '_id' => strval($ticket->_id),
                    'field' => $field,
                    'oldValue' => $oldValue,
                    'newValue' => $value,
                ];

                // отослали нотификацию в очередь
                \Crm\Helpers\MailQueue::sendToMailQueue($dataForNotification);

            }
            return array('success'=>true);
        } else {
            echo 'This end- point accepts only Ajax requests';
        }
    }

    private function changeStatusOptionsWithFieldSave($field, $value, $ticket)
    {
        switch ($field) {
            case 'status':
                if ($ticket->status == 'New' && in_array($value, ['Assigned', 'Need Assistance', 'Verify & Close', 'Reassign', 'Client Review'])) {

                } else {
                    if ($value == 'New') {

                    } elseif ($ticket->status == 'Closed' && $value != 'Closed') {
                        $value = 'Reassign';
                        $ticket->$field = $value;

                    } else {
                        $ticket->$field = $value;
                    }
                }
                break;

            case 'assignTo':
                if ($ticket->status == 'New') {
                    $ticket->status = 'Assigned';
                } elseif ($ticket->status == 'Assigned') {
                    $ticket->status = 'Reassign';
                } elseif (in_array($ticket->status, ['Closed', 'Client Review', 'Verify & Close'])) {
                    $ticket->status = 'Reassign';
                }
                $ticket->$field = $value;
                break;

            default:
                $ticket->$field = $value;
                break;
        }

        if (!$ticket->save()) {
            throw new \Exception('Error while saving ticket');
        }

        return;
    }

    public function postcommentAction()
    {
        $this->view->disable();
        $request = new \Phalcon\Http\Request();

        $identity = $this->auth->getIdentity();

        $items = [];
        $msg = false;
        $html = '';

        if ($request->isPost() == true && $request->isAjax() == true) {
            $ticketID = $this->dispatcher->getParam(0);

            $ticket = Tickets::findById($ticketID);

            if (!$ticket) {
                throw new \Exception('Ticket with specified Id doesn\'t exists');
            }

            $data = $request->getPost();

            if (!array_key_exists('msg', $data) ||
                !array_key_exists('type', $data) ||
                !in_array($data['type'], ['private', 'public'])
            ) {

                throw new \Exception('Error in in params for API');
            }

            $filter = new \Phalcon\Filter;
            $data['msg'] = $filter->sanitize($data['msg'], 'striptags');

            $comment = new \Crm\Models\Comments;

            $comment->parent_name = 'tickets';
            $comment->parent_id = strval($ticketID);
            $comment->user_id = $identity['id'];
            $comment->user_name = $identity['name'];

            $comment->isPrivate = ($data['type'] == 'private') ? true : false;

            $comment->text = $data['msg'];

            if (array_key_exists('notifiers', $data) && is_array($data['notifiers'])) {
                $comment->notified = $data['notifiers'];
            }

            if (!$comment->save()) {
                throw new \Exception('Error while saving comment');
            }

            $emails = [];

            if ($data['type'] == 'public') {
                //@todo check if $data['notifiers'] - in correct data format
                if ($data['notifiers']) {
                    $emails = \Crm\Helpers\UsersHelper::getEmailsByUserIds($data['notifiers']);
                }

                $emailData = \Crm\helpers\TicketsHelper::getExternalTicketAuthorData($ticketID);

                if ($emailData) {
                    $emails['out'] = $emailData['email'];
                }
            } elseif ($data['type'] == 'private') {
                if ($ticket->notify) {

                    $emails = \Crm\Helpers\UsersHelper::getEmailsByUserIds($ticket->notify);
                }
            }

            $department = \Crm\Helpers\DepartmentsHelper::checkIfConfigured($ticket->department);

            if ($department) {
                $d = [
                    'emails' => $emails,
                    'text' => $data['msg'] . '<br><br><a href="' . $this->config->application->baseUri . '/tickets/viewTicket/' . $ticketID . '">Jump to ticket</a>',
                    'subject' => 'New comment on ticket ' . $ticket->subject,
                    'department' => $department
                ];

                if ($this->config->notifications_by_mail->watchers) {
                    if ($data['type'] == 'private') {
                        \Crm\Helpers\CommentsMail::sendReplyForPrivateComment($d);
                    } elseif ($data['type'] == 'public') {
                        $d['subject'] = $data['subject'];
                        \Crm\Helpers\CommentsMail::sendReplyForPublicComment($d);
                    }
                }

            } else {
                // log - incorrect department, or misconfiguration
            }

            $success = true;

            $comments = \Crm\Helpers\CommentsHelper::commentsView($ticketID);

            $items = $comments;

            $this->view->setVar('comments', $comments);
            $this->view->setVar('needAnimate', true);

            $parameters = ['comments' => $comments, 'needAnimate' => true];
            $html = $this->view->getRender('tickets', 'partials/comments/list', $parameters, function ($view) {
                $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
            });

        } else {
            $msg = 'This end- point accepts only Ajax requests';
            $success = false;
        }

        header('Content-type: application/json');
        echo json_encode(['success' => $success, 'items' => $items, 'msg' => $msg, 'html' => $html]);

    }

    public function getCommentsAction()
    {
        $this->view->disable();
        $request = new \Phalcon\Http\Request();

        $items = [];
        $msg = $success = $html = false;

        if ($request->isPost() == true && $request->isAjax() == true) {
            $ticketID = $this->request->getPost('ticket_id');

            if (!$ticketID) {
                throw new \Exception('Incorrect ticket object id');
            }

            $comments = \Crm\Helpers\CommentsHelper::commentsView($ticketID);

            $this->view->setVar('comments', $comments);
            $this->view->setVar('needAnimate', true);

            $parameters = ['comments' => $comments, 'needAnimate' => true];

            $html = $this->view->getRender('tickets', 'partials/comments/list', $parameters, function ($view) {
                $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
            });

            $success = true;

        } else {
            $msg = 'This end- point accepts only Ajax requests';
            $success = false;
        }

        header('Content-type: application/json');
        echo json_encode(['success' => $success, 'html' => $html, 'msg' => $msg]);

    }

    public function getBatchDataAction()
    {
        $this->view->disable();
        $request = new \Phalcon\Http\Request();

        $items = [];
        $msg = $success = false;

        if ($request->isGet() == true && $request->isAjax() == true) {

            $ticketID = $this->dispatcher->getParam(0);

            if (!$ticketID) {
                throw new \Exception('Incorrect ticket object id');
            }

            $ticket = Tickets::findById($ticketID);

            if (!$ticket) {
                throw new \Exception('Ticket with specified Id doesn\'t exists');
            }

            // .. transform code here
            $allUsers = \Crm\Helpers\UsersHelper::getPairsUsernameWithId();

            $ticket->toArray();

            $transformedResult = [
                'users' => $allUsers,
                'assign_to' => $ticket->assignTo,
                'notify' => $ticket->notify,
                'type' => $ticket->type,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'department' => $ticket->department,
                'deadline' => \Crm\Helpers\DateTimeFormatter::reverseDeadlineFormatter($ticket->deadline),
            ];

            $items = $transformedResult;
            $success = true;


        } else {
            $msg = 'This end- point accepts only Ajax GET requests';
            $success = false;
        }


        header('Content-type: application/json');
        echo json_encode(['success' => $success, 'items' => $items, 'msg' => $msg]);
    }

    public function historyAction()
    {
        $this->view->disable();
        $request = new \Phalcon\Http\Request();
        if ($request->isGet() == true && $request->isAjax() == true) {
            $ticketId = $this->request->get('ticket_id');
            $ticketEvents = new \Crm\Models\Services\TicketsEvents();
            $rows = $ticketEvents::find(array(
                array(
                    'ticket_id' => new MongoId($ticketId)
                ),
                'sort' => array(
                    'created_at' => -1
                ),
                'limit' => 100
            ));
            $resRows = array();
            foreach ($rows as $row) {
                $resRows[] = Date('Y-m-d H:i:s',$row->created_at->sec).' | '.$row->user_name.' | '.$row->description;
            }
            if ($resRows == array()) {
                return json_encode(array('No history'));
            } else {
                return json_encode($resRows);
            }
        } else {
            return json_encode(array('No history'));
        }
    }

    public function otherTicketsUserAction()
    {
        $this->view->disable();
        $request = new \Phalcon\Http\Request();
        if ($request->isGet() == true && $request->isAjax() == true) {
            $userId = $this->request->get('user_id');
            $ticketId = $this->request->get('ticket_id');
            $tickets = new \Crm\Models\Tickets();
            $rows = $tickets::find(array(
                array(
                    'assignTo' => $userId,
                    'status' => array('$ne' => 'Closed')
                ),
                'sort' => array(
                    'created' => -1
                ),
                'limit' => 100
            ));
            $resRows = array();
            foreach ($rows as $row) {
                if ($ticketId == (string)$row->_id) {
                    continue;
                }
                $resRows[] = array(
                    'id' => (string)$row->_id,
                    'subject' => $row->subject,
                );
            }
            if ($resRows == array()) {
                return json_encode(array(array(
                    'id' => '0',
                    'subject' => 'No other tickets'
                )));
            } else {
                return json_encode($resRows);
            }
        } else {
            return json_encode(array('No other tickets'));
        }
    }

    public function createTaskAction()
    {
        $this->view->disable();
        $request = new \Phalcon\Http\Request();

        $emailToTicketMapping = [
            'priority' => function ($email) {
                $out = 5;
                return $out;
            },
            'status' => function ($email) {
                $out = 'New';
                return $out;
            },
            'type' => function ($email) {
                $out = 'Task';
                return $out;
            },
            'name' => function ($email) {
                $out = $email->fromAddress;
                return $out;
            },
            'department' => function ($email) {
                $out = 'Common';
                return $out;
            },
            'subject' => function ($email) {
                $out = $email->subject;
                return $out;
            },
            'description' => function ($email) {
                $out = ('Mail from: ' . $email->mailDate . '<br>\n\r<br>\n\r<br>\n\r' . $email->body);
                return $out;
            },
            'authorId' => function ($email) {
                $out = '';
                return $out;
            },
            'created' => function ($email) {
                $out = '';
                return $out;
            },
            'authorName' => function ($email) {
                $out = '';
                return $out;
            },
            'notify' => function ($email) {
                $out = '';
                return $out;
            },
            'reportedBy' => function ($email) {
                $out = \Crm\Helpers\UsersHelper::getCurrentUserId();
                return $out;
            },
            'isFromEmail' => function ($email) {
                $out = true;
                return $out;
            },
            'messageId' => function ($email) {
                $out = $email->messageId;
                return $out;
                // there are 2 messageIds , - 1. internal; 2. EmailId by RFC

            },
            ////'' => function($email) {$out = '';  return $out;},
        ];

        if ($request->isPost() == true && $request->isAjax() == true) {
            $query = $this->request->getPost();
            $emailInternalID = $query['_id'];

            //@todo validation - if email exists in Db
            $email = \Crm\Models\Email::findById($emailInternalID);

            $task = new \Crm\Models\Tickets();

            foreach ($emailToTicketMapping as $k => $v) {
                //simple mapping
                $task->{$k} = $v($email);
            }

            if (!$task->save()) {
                throw new \Exception('Error whil creating task from email');
            }
        }

        return ['success' => true];
    }

    public function sendmailAction()
    {
        $this->view->disable();
        $request = new \Phalcon\Http\Request();

        if ($request->isPost() == true && $request->isAjax() == true) {
            $emailId = $this->dispatcher->getParams(0);
            $mailData = $request->getPost();

            $subject = $mailData['subject'];
            $body = $mailData['msg'];
            // notifiers, type

            try {
                if (!is_array($emailId) || !($email = \Crm\Models\Email::findById($emailId[0]))) {

                }
            } catch (Exception $e) {
                return ['success' => false, 'message' => ($e->getCode() . ' : ' . $e->getMessage())];
            }

            if (empty($email)) {
                return ['success' => false];
            }
            /*
            $data = [
                'messageId' => $email->messageId,
                'inReplyTo' => $email->inReplyTo,
                'references' => $email->references,
                'subject' => $email->subject,
            ];*/

            // по - inReplyTo - выбрали сообщение
            $rootEmail = \Crm\Models\Email::find([['messageId' => $email->inReplyTo]]);

            if (!$rootEmail) {
                // если нет сообщения - нужно перестраивать цепочку
            }
            // rootEmail - всегда к нам сообщение
            $rootEmail = $rootEmail[0];

            preg_match("/<(.*?)>/", $rootEmail->fromAddress, $toAddress);
            if (!((sizeof($toAddress) > 0) && $toAddress = $toAddress[1])) {
                $toAddress = $rootEmail->fromAddress;
            }
            // тут можно воткнуть кучу разных проверок - например, есть ли такой е- мейл, т.д.

            $subject = "Re: " . $email->subject;

            $mailer = $this->getDI()->get("Mailer");

            // пофиксить референсес
            $references = $rootEmail->messageId;
            $inReplyTo = $rootEmail->messageId;

            // Adding custom headers
            $customHeaders = [
                'In-Reply-To' => $inReplyTo,
                'References' => $references,
            ];

            // Неверно по структуре. Большая связанность кода. Нужно - вынести отдельно, в адаптер, либо - бридж
            //   вот эту часть, где - получение параметров
            $msgRaw = $mailer->sendReturningRawMessage($subject, $body, [$toAddress => $toAddress], $customHeaders);
            // Переделать - постановки - а, в очередь

            // подготовка спец символов для монго (нельзя чтоб в ключе бьла б ".", а Ыцшае возвращает ключ с точкой)
            $toAddress = \Crm\Helpers\MongoKeySupportHelper::directArrayTransform($msgRaw->getTo());
            $fromAddress = \Crm\Helpers\MongoKeySupportHelper::directArrayTransform($msgRaw->getFrom());

            $emailParams = [
                'body' => $body,
                'subject' => $msgRaw->getSubject(),
                'date' => $msgRaw->getDate(),
                'isInSent' => true,
                'inReplyTo' => $inReplyTo,
                'references' => $references,
                'mailDate' => $msgRaw->getDate(),
                'toAddress' => $toAddress,
                'fromAddress' => $fromAddress,
                'attachments' => null,
                'messageId' => $msgRaw->getId(),
            ];

            $newEmail = \Crm\Models\CollectionFactory::getNewInstanceOf('Email', $emailParams);

            if (!$newEmail->save()) {
                // Exception
            }

            // запись в thread
            $thread = \Crm\Models\MailThreads::find([['parentId' => $email->inReplyTo]]);

            if (!$thread) {
                $thread = \Crm\Models\CollectionFactory::getNewInstanceOf('MailThreads',
                    ['parentId' => $email->inReplyTo,
                        'childrens' => [$msgRaw->getId()]]
                );
            } else {
                $thread = $thread[0];
            }
            $thread->childrens[] = $msgRaw->getId();

            if ($thread->save()) {
                // Exception
            }

            // тяжело читаемый код. Разбить - более мелкие Части. По- функциям - классам
            ob_start();
            print_r($msgRaw->toString());
            print_r(get_class_methods($msgRaw));
            file_put_contents("/tmp/msg", ob_get_clean());
            // Нашли цепочку, к которой привязан е- мейл.

            // привязали туда е- мейл + сохранили в БД (с указанием references & inReplyTo)

            return;

        }
    }
}