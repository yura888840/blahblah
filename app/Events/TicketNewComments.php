<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 25.05.15
 * Time: 19:24
 */

namespace Crm\Events;


class TicketNewComments extends EventBase
{
    public $text;

    public $mailTpl = 'notification';

    public $emailBody;

}