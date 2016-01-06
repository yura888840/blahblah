<?php

/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 14.08.15
 * Time: 16:00
 */
class GearmanclientTask extends \Phalcon\CLI\Task
{

    public function mainAction()
    {
        exit('Not supported. Try : gearmanclient t' . PHP_EOL);

        $server = (isset($this->config->gearman->server))
            ? $this->config->gearman->server
            : "";

        $client = new GearmanClient();
        $client->addServer($server);

        echo PHP_EOL;
        print_r($client->do('reverse', 'Testtt'));
        echo PHP_EOL . PHP_EOL;
    }

    public function aAction()
    {
        $res = \Phalcon\DI::getDefault()->get('gearmanClient');

        $data = ['e-mail' => 'my@email.com', 'subj' => 'subject', 'fromDepartment' => 'technical'];
        $data = serialize($data);

        $r = $res->doBackground("MailSend", $data);
        var_dump($r);

        ///$r = $res->doNormal("ExampleFunction", $data);
        ///var_dump($r);
    }

    public function clearopcacheAction()
    {
        apc_clear_cache();
        apc_clear_cache('user');
        apc_clear_cache('opcode');
        opcache_reset();
    }

    public function tAction()
    {


        $server = (isset($this->config->gearman->server))
            ? $this->config->gearman->server
            : "";

        $client = new GearmanClient();
        $client->addServer($server);

        //echo 'Sending job .. ' . PHP_EOL;
        $res = \Phalcon\DI::getDefault()->get('gearmanClient');

        print_r($res);


        $server = (isset($this->config->gearman->server))
            ? $this->config->gearman->server
            : "";

        $client = new GearmanClient();
        $client->addServer($server);
        var_dump($server);
        var_dump($client);


        //->doNormal("ExampleFunction", "Hello World!");
        $r = $res->doBackground("ExampleFunction", 'sdfs'/*, "Hello World!"*/);

        $r = $client->doBackground("ExampleFunction", 'sdfs'/*, "Hello World!"*/);


        die();
        //print $res."\n";
        //var_dump($res);die('**');

        //print_r($client->do('example', ''));
    }
}