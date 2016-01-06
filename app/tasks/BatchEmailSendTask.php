<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 25.08.15
 * Time: 19:43
 */


/**
 * Class BatchEmailSendTask
 *
 * Main responsibilities - send e-mails from storage to recepients
 */
class BatchEmailSendTask extends \Phalcon\CLI\Task
{

    public function mainAction()
    {
        echo 'Sending e-mails from storage .. ' . PHP_EOL;

        $di = \Phalcon\DI::getDefault();

        $storage = \Phalcon\DI::getDefault()->get('redis');


        $redisCache = $di->get('redisCache');

        ///$redisCache->save('my-data5', array(1, 2, 3, 4, 5));

        $data = $redisCache->get('my-data5');

        var_dump($data);

        //var_dump($storage);
    }
}