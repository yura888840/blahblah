<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 23.02.15
 * Time: 15:04
 */

namespace Crm\Mail;

use Phalcon\Mvc\User\Component;
use Swift_Message as Message;
use Swift_SmtpTransport as Smtp;
use Phalcon\Mvc\View;

/**
 * Class Mail - send EMail to recepients
 * @package Crm\Mail
 */
class Mail extends Component {

    protected $transport;

    /**
     * Applies a template to be used in the e-mail
     *
     * @param string $name
     * @param array $params
     */
    public function getTemplate($name, $params)
    {
        $parameters = array_merge(array(
            'publicUrl' => $this->config->application->baseUri
        ), $params);

        $tpl = $this->view->getRender('emailTemplates', $name, $parameters, function ($view) {
            $view->setRenderLevel(View::LEVEL_LAYOUT);
        });

        return $tpl;
    }

    /**
     * Sends e-mails based on predefined templates
     *
     * @param array $to
     * @param string $subject
     * @param string $name
     * @param array $params
     */
    public function send($to, $subject, $name, $params)
    {
        $mailSettings = $this->config->mailer;

        return $this->sendUsingMailSettings($to, $subject, $name, $params, $mailSettings);
    }

    public function sendFromAddress($to, $subject, $name, $params, $fromDepartment)
    {
        $mailSettings = $this->getEmailCredentialsFor($fromDepartment);

        return $this->sendUsingMailSettings($to, $subject, $name, $params, $mailSettings);
    }

    private function getEmailCredentialsFor($department)
    {
        $mailConfigs = $this->config->mail_fetcher;

        foreach ($mailConfigs as $departmentFromConfig => $params) {
            if ($departmentFromConfig == $department) {
                return $params;
            }
        }

        return null;
    }

    private function sendUsingMailSettings($to, $subject, $name, $params, $mailSettings)
    {

        $template = $this->getTemplate($name, $params);

        // Create the message
        $message = Message::newInstance()
            ->setSubject($subject)
            ->setTo($to)
            ->setFrom(array(
                $mailSettings->fromEmail => $mailSettings->fromName
            ))
            ->setBody($template, 'text/html');

        if (!$this->transport) {
            $this->transport = Smtp::newInstance(
                $mailSettings->host,
                $mailSettings->port,
                $mailSettings->security
            )
                ->setUsername($mailSettings->username)
                ->setPassword($mailSettings->password);
        }

        $mailer = \Swift_Mailer::newInstance($this->transport);

        return $mailer->send($message);
    }

}