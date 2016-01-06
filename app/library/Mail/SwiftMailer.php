<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 02.09.15
 * Time: 18:33
 */

namespace Crm\Mail;

use Phalcon\Mvc\User\Component;
use Swift_Message as Message;
use Swift_SmtpTransport as Smtp;

class SwiftMailer extends Component
{

    public function send($to, $subject, $body)
    {
        $result = $this->realSend($to, $subject, $body);
        return $result[0];
    }

    public function sendReturningRawMessage($to, $subject, $body, $customHeaders = null)
    {
        $result = $this->realSend($to, $subject, $body, $customHeaders);
        return $result[1];
    }

    private function realSend($to, $subject, $body, $customHeaders = null)
    {
        $mailSettings = $this->config->mailer;

        // Create the message
        $message = Message::newInstance()
            ->setSubject($subject)
            ->setTo($to)
            ->setFrom(array(
                $mailSettings->fromEmail => $mailSettings->fromName
            ))
            ->setBody($body, 'text/html');

        if ($customHeaders) {
            foreach ($customHeaders as $headerName => $headerValue) {
                $message->getHeaders()->addTextHeader($headerName, $headerValue);
            }
        }

        $this->transport = Smtp::newInstance(
            $mailSettings->host,
            $mailSettings->port,
            $mailSettings->security
        )
            ->setUsername($mailSettings->username)
            ->setPassword($mailSettings->password);


        $mailer = \Swift_Mailer::newInstance($this->transport);

        return [$mailer->send($message), $message];
    }

}