<?php

namespace Crm\WebSocket;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class TicketSocket implements MessageComponentInterface {
    protected $clients;
    protected $dataResource = array();

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);
        echo "New connection!  ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $msgArray = json_decode($msg);
        if ($msgArray->action == 'open'){
            $this->dataResource[$from->resourceId]['userName'] = $msgArray->username;
            $this->dataResource[$from->resourceId]['resource'] = $msgArray->resource;
            $msg = array();
            $msg['action'] = 'open';
            $msg['data'] = $msgArray->username;
            $msg['connection'] = $this->dataResource;
            foreach ($this->clients as $client) {
                $msgJson = json_encode($msg);
                $client->send($msgJson);
            }
        }
        if ($msgArray->action == 'getUserOnline'){
            $this->dataResource[$from->resourceId]['userName'] = $msgArray->username;
            $this->dataResource[$from->resourceId]['resource'] = $msgArray->resource;
            $msg = array();
            $msg['action'] = 'getUserOnline';
            $msg['data'] = $msgArray->username;
            $msg['connection'] = $this->dataResource;
            $msgJson = json_encode($msg);
            $from->send($msgJson);
        }

        if ($msgArray->action == 'updateTicket'){
            $this->dataResource[$from->resourceId]['userName'] = $msgArray->username;
            $this->dataResource[$from->resourceId]['resource'] = $msgArray->resource;
            $msg = array();
            //передаем кто поменял и что поменял
            //pass someone changed and what has changed
            $msg['action'] = 'updateTicket';
            $msg['userName'] = $msgArray->username;
            $msg['resource'] = $msgArray->resource;
            $msg['data'] = $msgArray->data;
            foreach ($this->clients as $client) {
                if (isset($this->dataResource[$client->resourceId])){
                    //передаем только другим пользователям на этой странице
                    //pass only other users on this page
                    if ($this->dataResource[$client->resourceId]['resource'] == $msgArray->resource and
                        $this->dataResource[$client->resourceId]['userName'] != $msgArray->username) {
                        $msgJson = json_encode($msg);
                        $client->send($msgJson);
                    }
                }
            }
        }

        if ($msgArray->action == 'updateCommentTicket'){
            $this->dataResource[$from->resourceId]['userName'] = $msgArray->username;
            $this->dataResource[$from->resourceId]['resource'] = $msgArray->resource;
            $msg = array();
            //передаем кто поменял и что поменял
            //pass someone changed and what has changed
            $msg['action'] = 'updateCommentTicket';
            $msg['userName'] = $msgArray->username;
            $msg['resource'] = $msgArray->resource;
            $msg['data'] = $msgArray->data;
            foreach ($this->clients as $client) {
                if (isset($this->dataResource[$client->resourceId])){
                    //передаем только другим пользователям на этой странице
                    //pass only other users on this page
                    if ($this->dataResource[$client->resourceId]['resource'] == $msgArray->resource and
                        $this->dataResource[$client->resourceId]['userName'] != $msgArray->username) {
                        $msgJson = json_encode($msg);
                        $client->send($msgJson);
                    }
                }
            }
        }

        $numRecv = count($this->clients) - 1;


        foreach ($this->clients as $client) {
            if ($from !== $client) {

            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        $msg = array();
        $msg['action'] = 'close';
        if (isset($this->dataResource[$conn->resourceId])) {
            $msg['data'] = $this->dataResource[$conn->resourceId]['userName'];
            unset($this->dataResource[$conn->resourceId]);
        }
        $msg['connection'] = $this->dataResource;
        foreach ($this->clients as $client) {
            $msgJson = json_encode($msg);
            $client->send($msgJson);
        }

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}