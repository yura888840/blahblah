<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 19.05.15
 * Time: 13:09
 */

namespace Crm\Mail;

interface IMailReceiver
{
    public function getLastMail();

    public function getOneMail($mbox, $uid);

    public function getMailListOneBox($imap_mailbox, $imap_login, $imap_pass, $criteria);
}