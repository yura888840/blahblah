<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 25.05.15
 * Time: 18:58
 */

namespace Crm\Helpers;

use \Crm\Mail\Mail;

class MailWrapper
{

    //@todo \Crm\Events\Ticket\NewComment
    public static function batchSend(array $emails, \Crm\Events\EventBase $event)
    {

        $mailer = \Phalcon\DI::getDefault()->getMail();

        foreach ($emails as $toEmail) {
            $mailer->send(
                [$toEmail => $toEmail],
                $event->text, $event->mailTpl, array(
                'body' => $event->emailBody,
            ));
        }
    }

}