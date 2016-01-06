<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 29.05.15
 * Time: 16:59
 */

namespace Crm\Helpers;

class CommentsMail
{
    const REPLY_TEMPLATE = 'response2ticket';
    const REPLY_TEMPLATE_EXTERNAL = 'response2ticketexternal';

    public static function sendReplyForPublicComment($data)
    {
        $emails = $data['emails'];
        $body = $data['text'];
        $subject = $data['subject'];
        $department = $data['department'];

        self::sendReplyToWatchers($data);
        if (array_key_exists('out', $emails)) {
            $mailer = \Phalcon\DI::getDefault()->getMail();
            $mailer->sendFromAddress(array(
                    $emails['out'] => $emails['out'])
                , $subject, self::REPLY_TEMPLATE_EXTERNAL, array(
                    'body' => $body,
                ),
                $department);
        }
    }

    /**
     * ALIAS FUNCTION. The same as sendReplyToWatchers
     * @param $data
     */
    public static function sendReplyForPrivateComment($data)
    {
        self::sendReplyToWatchers($data);
    }

    // Needs to be @refactored
    private static function sendReplyToWatchers($data)
    {
        $emails = $data['emails'];
        $body = $data['text'];
        $subject = $data['subject'];
        $department = $data['department'];

        $mailer = \Phalcon\DI::getDefault()->getMail();

        foreach ($emails as $k => $email) {
            if ($k === 'out')
                continue;

            $mailer->sendFromAddress(array(
                    $email => $email)
                , $subject, self::REPLY_TEMPLATE, array(
                    'body' => $body,
                ),
                $department);
        }
    }
}