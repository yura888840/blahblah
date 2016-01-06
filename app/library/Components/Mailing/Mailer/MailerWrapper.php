<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 28.08.15
 * Time: 19:27
 */

namespace Crm\Components\Mailing\Mailer;


class MailerWrapper
{

    private $mailer;

    public function __construct($mailer = NULL)
    {
        if ($mailer) {
            $this->mailer = $mailer;
        }

    }

    /**
     * Multi send
     * @param $emailSubject
     * @param $emailBody
     * @param $recepients
     * @param bool|true $showMessage
     * @return string
     */
    public function send($emailSubject, $emailBody, $recepients, $showMessage = true)
    {

        $result = "Successfully sent";

        foreach ($recepients as $recepient) {
            $this->mailer->send($recepient, $emailSubject, $emailBody);
        }
        if ($showMessage) {
            echo $result . PHP_EOL;
        }

        return $result;
    }

    public function sendReturningRawMessage($emailSubject, $emailBody, $recepient, $customHeaders = null)
    {
        return $this->mailer->sendReturningRawMessage($recepient, $emailSubject, $emailBody, $customHeaders);
    }

}