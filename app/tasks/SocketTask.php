<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Crm\WebSocket;


use Phalcon\Session\Adapter\Files as SessionAdapter;

class SocketTask extends \Phalcon\CLI\Task
{

    public function ticketAction()
    {
        $config = include __DIR__ . "/../config/config.php";
        try {
            $server = IoServer::factory(
                new HttpServer(
                    new WsServer(
                        new Crm\WebSocket\TicketSocket()
                    )
                ),
                $config->webSocket->port,
                '127.0.0.1'
            );
        }
        catch (\Exception $e) {
            echo 'no connect, port already'.PHP_EOL;
            return;
        }

        print_r($config->webSocket);
        $server->run();
    }
}