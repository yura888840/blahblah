<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 03.03.15
 * Time: 15:38
 */

use Crm\Models\Tickets;
use Crm\Mail\mailFetcher;

class EmailfetchTask extends \Phalcon\CLI\Task
{

    public function mainAction() {
        echo 'Processing..\r\n';

        $fetcher = new mailFetcher();

    }
}