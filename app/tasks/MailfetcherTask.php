<?php

use Crm\Models\Tickets;
use Crm\Helpers\DataSets;
use Crm\Mail;
use Crm\Models\MessagesUids;

class MailfetcherTask extends \Phalcon\CLI\Task
{

    public $mailReceiver;

    public function beforeAction()
    {
        error_reporting(0);
        $this->mailReceiver = new Mail\MailReceiver();
    }

    public function lastAction()
    {
        $this->beforeAction();
        return $this->mailReceiver->getLastMail();
    }

    public function mainAction()
    {
        $this->beforeAction();
        $this->fetch();
    }

    /**
     * РАБОЧАЯ ФУНКЦИЯ
     *
     */
    public function pollAction()
    {
        $this->beforeAction();
        $mBoxes = $this->config->mail_fetcher;

        $processed = [];
        foreach ($mBoxes as $department => $box) {

            if (!isset($box->mailbox) || empty($box->mailbox)) {
                continue;
            }

            if (!isset($box->mailbox) || empty($box->username)) {
                continue;
            }

            if (!isset($box->password) || empty($box->password)) {
                continue;
            }

            if (array_key_exists($box->mailbox . $box->username, $processed)) {
                continue;
            }

            $processed[$box->mailbox . $box->username] = $department;

            try {

                $mbox = imap_open($box->mailbox, $box->username, $box->password);

                if (imap_errors() || imap_alerts()) {
                    throw new \Exception('Error on connecting to mailbox for : ' . $department);
                }

                $resultNegativeMsg = $this->processMailbox($mbox, $department);

                if ($resultNegativeMsg) {
                    throw new \Exception($resultNegativeMsg);
                }

            } catch (Exception $e) {
                echo $e->getMessage() . PHP_EOL;
            }


        }
    }

    /**
     * Get all e-mails from mailbox for past 1 day && process
     * @param $mbox
     * @return bool negative - result msg
     */
    /**
     * Эта функция привязана - к test Action
     *
     * fetch - сюда не имеет отношения
     */
    private function processMailbox($mbox, $department)
    {
        $di = Phalcon\DI::getDefault();
        $needPurgeMB = $di->config->tweaking->need_purge;
        $needPurgeMB = !empty($needPurgeMB) ? $needPurgeMB : false;

        if ($msg_numbers_list = imap_search($mbox, 'SINCE ' . date("d-M-Y", strtotime('-1000 days')))) {
            krsort($msg_numbers_list);

            foreach ($msg_numbers_list as $msg_number) {
                $header = imap_header($mbox, $msg_number);
                $structure = imap_fetchstructure($mbox, $msg_number);

                ob_start();
                var_dump($header);
                file_put_contents("/tmp/header", ob_get_clean(), FILE_APPEND);

                $message = array();
                $message['subject'] = $this->mailReceiver->text_decode($header->subject);
                $message['from_name'] = (isset($header->from[0]->personal) ? $this->mailReceiver->text_decode($header->from[0]->personal) : '');
                $message['from_email'] = $header->from[0]->mailbox . '@' . $header->from[0]->host;
                $message['date'] = $header->date;
                $message['message_id'] = $header->message_id;

                $email = [];
                $email['message_id'] = $header->message_id;
                $email['MailDate'] = $header->MailDate;
                $email['fromaddress'] = $header->fromaddress;

                $email['messageId'] = $header->message_id;
                $email['fromAddress'] = $header->fromaddress;
                $email['mailDate'] = $header->MailDate;
                $email['toAddress'] = $header->toaddress;
                $email['mailDate'] = $header->MailDate;
                $email['inReplyTo'] = $header->in_reply_to;
                // @todo подчистить ненужные переменные

                // с этими - toadress - нужно глядеть. Их как- то много, и на что ответы, т.д.
                $email['toaddress'] = $header->toaddress;
                $email['date'] = $header->date;
                $email['subject'] = $header->subject;
                $email['in_reply_to'] = property_exists($header, 'in_reply_to') ? $header->in_reply_to : NULL;
                $email['references'] = property_exists($header, 'references') ? $header->references : NULL;

                // BODY & attachments
                $part_array = $this->mailReceiver->create_part_array($structure);
                if ($part_info = $this->mailReceiver->get_part_info_by_subtype($part_array, 'HTML')) {
                    $message['body'] = $this->mailReceiver->mime_encode($this->mailReceiver->decode($part_info['encoding'], imap_fetchbody($mbox, $msg_number, $part_info['part_number'])), $part_info['parameters']);
                } elseif ($part_info = $this->mailReceiver->get_part_info_by_subtype($part_array, 'PLAIN')) {
                    $message['body'] = $this->mailReceiver->mime_encode($this->mailReceiver->decode($part_info['encoding'], imap_fetchbody($mbox, $msg_number, $part_info['part_number'])), $part_info['parameters']);
                    $message['body'] = $this->mailReceiver->format_text_message($message['body']);
                }

                $message['attachments'] = $this->mailReceiver->get_attachments($part_array);

                //@todo вот это временно. До состояния целостности
                $email['body'] = $message['body'];
                $email['attachments'] = $message['attachments'];
                // @todo нужно покрутить коллекцию - Email

                $uid = $msg_number;

                if ($message['attachments']) {
                    $message['attachments_dir'] = $this->mailReceiver->insert_attachments_mailbox($mbox, $uid, $message);
                }

                // @todo нужно привести к единому виду - $message & $email
                var_dump($email);
                $mailItem = $this->mailReceiver->protect_html_text($message);

                $ticketParams = [
                    'department' => $department,
                ];

                $this->createTicketOnEmailItem($mailItem, $ticketParams, $email);

                if ($needPurgeMB) {
                    imap_delete($mbox, $msg_number);
                }
            }
        }

        //Delete all messages marked for deletion
        if ($needPurgeMB) {
            imap_expunge($mbox);
        }

        //Close an IMAP stream
        imap_close($mbox);

        return false;
    }


    // @todo внести некую ясность, целостность в параметры
    private function createTicketOnEmailItem($mailItem, $ticketParams, $email = NULL)
    {
        // тут происходит - проверка - если есть в БД - возврат true
        //   Если нет в БД - сохраняем, и + возврат False
        if ($this->checkIfEmailAlreadyProcessed($mailItem['message_id'])) {
            return;
        }
        $this->saveTicketOnMailitem($ticketParams, $mailItem);
        $this->saveEmailOnMappedData($email);
    }

    // вот не нравится мне тут это - ticketParams
    private function saveTicketOnMailitem($ticketParams, $mailItem)
    {
        $ticket = new Tickets();

        if ($v = DataSets::getDefaultValueFor('TicketsStatus')) $ticket->status = $v;
        if ($v = DataSets::getDefaultValueFor('TicketsTypes')) $ticket->type = $v;
        if ($v = DataSets::getDefaultValueFor('TicketsPriority')) $ticket->priority = $v;

        $ticket->department = $ticketParams['department'];

        $ticket->subject = strip_tags($mailItem['subject']);
        $ticket->description = strip_tags($mailItem['body']);
        $ticket->userName = strip_tags($mailItem['from_name']);
        $ticket->fromEmail = strip_tags($mailItem['from_email']);
        $ticket->messageId = $mailItem['message_id'];
        $ticket->isFromEmail = true;

        if (!$ticket->save()) {
            throw new \Exception('Error while creating ticket from email');
        }
    }

    private function saveEmailOnMappedData($data)
    {
        //@todo пофиксить маппинг. Либо - убрать оный
        /*
        $mapping = \Crm\Models\Email::$mapping;
        foreach($mapping as $v)
        {
            if(array_key_exists($v, $data))
            {
                $data[$mapping[$v]] = $data[$v];
                unset($data[$v]);
            }
        }*/


        echo '***********************************' . PHP_EOL;

        var_dump($data);
        $email = \Crm\Models\CollectionFactory::getNewInstanceOf('Email', $data);

        var_dump($email);

        if (!$email->save()) {
            throw new \Exception('Error while creating email in system');
        }
    }


    /**
     * @param $uid
     * @return bool
     * @throws Exception
     */

    /**
     * Эту логику нужно перестроить
     *
     */
    /**
     * Убрать отсюда проверку в таске uid
     *
     * Ага. Оно же делает и сохранение. Тогда неверно название сущности
     */
    private function checkIfEmailAlreadyProcessed($uid)
    {
        $isExists = MessagesUids::find([['messageUid' => $uid]]);

        if (!$isExists) {
            $mUid = new MessagesUids();

            $mUid->messageUid = $uid;

            if (!$mUid->save()) {
                throw new \Exception('Error saving e-mail message Uid');
            }

            return false;
        }

        return true;
    }

    public function fetch()
    {
        try {
            foreach ($this->config->mailfetcher->imap as $imap) {
                $imap_server = $imap->host;
                $imap_login = $imap->username;
                $imap_pass = $imap->password;
            }

        } catch (Exception $e) {
            exit($e->getMessage());
        }

        echo "Opening mailbox ... " . PHP_EOL;
        $mbox = imap_open("{" . $imap_server . ":993/imap/ssl/novalidate-cert}INBOX", $imap_login, $imap_pass) or die("Error: can't connect: " . imap_last_error());

        if ($msg_numbers_list = imap_search($mbox, 'SINCE ' . date("d-M-Y", strtotime('-1 day')))) {
            krsort($msg_numbers_list);

            foreach ($msg_numbers_list as $msg_number) {
                $message = array();

                $header = imap_header($mbox, $msg_number);
                $structure = imap_fetchstructure($mbox, $msg_number);

                $message['subject'] = $this->mailReceiver->text_decode($header->subject);
                $message['from_name'] = (isset($header->from[0]->personal) ? $this->text_decode($header->from[0]->personal) : '');
                $message['from_email'] = $header->from[0]->mailbox . '@' . $header->from[0]->host;
                $message['date'] = $header->date;
                $message['message_id'] = $header->message_id;

                $part_array = $this->mailReceiver->create_part_array($structure);

                if ($part_info = $this->mailReceiver->get_part_info_by_subtype($part_array, 'HTML')) {
                    $message['body'] = $this->mailReceiver->mime_encode($this->mailReceiver->decode($part_info['encoding'], imap_fetchbody($mbox, $msg_number, $part_info['part_number'])), $part_info['parameters']);
                } elseif ($part_info = $this->mailReceiver->get_part_info_by_subtype($part_array, 'PLAIN')) {
                    $message['body'] = $this->mailReceiver->mime_encode($this->mailReceiver->decode($part_info['encoding'], imap_fetchbody($mbox, $msg_number, $part_info['part_number'])), $part_info['parameters']);
                    $message['body'] = $this->mailReceiver->format_text_message($message['body']);
                }

                $message['attachments'] = $this->mailReceiver->get_attachments($part_array);

                $message = $this->mailReceiver->protect_html_text($message);

                $action = 'create_new_ticket';
                if ($action == 'create_new_ticket') {
                    $tickets = new Tickets();
                    $tickets->usersId = null;

                    if ($v = DataSets::getDefaultValueFor('TicketsStatus')) $tickets->status = $v;
                    if ($v = DataSets::getDefaultValueFor('TicketsTypes')) $tickets->type = $v;
                    if ($v = DataSets::getDefaultValueFor('TicketsPriority')) $tickets->priority = $v;

                    $defaultDepartment = $this->config->mail_fetcher->default_department;

                    if (!$defaultDepartment) {
                        throw new \Exception('Error in config - mail_fetcher::default_department');
                    }

                    $tickets->department = $defaultDepartment;
                    $tickets->name = $message['subject'];
                    $tickets->description = $message['body'];
                    $tickets->userName = $message['from_name'];
                    $tickets->userEmail = $message['from_email'];
                    $tickets->messageId = $message['from_email'];

                    $tickets->save();

                    $this->saveEmailOnMappedData($message);
                    // Вот
                    /// !!! attachments

                    /////$message = $this->insert_attachments_tickets($mbox,$msg_number,$message['attachments'],$tickets->_id,'tickets',$message);

                    ///$tickets->setDescription($message['body']);
                    if (!$tickets->save()) {
                        throw new \Exception('Error while saving ticket');
                    }


                }

                imap_delete($mbox, $msg_number);

            }
        }

        //Delete all messages marked for deletion
        imap_expunge($mbox);

        //Close an IMAP stream
        imap_close($mbox);
    }
}