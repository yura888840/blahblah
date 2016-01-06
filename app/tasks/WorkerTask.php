<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 19.05.15
 * Time: 17:14
 */
class WorkerTask extends \Phalcon\CLI\Task
{

    private $functions = [
        'reverse1' => array('WorkerTask', "reverse_fn"),
    ];

    /**
     * Core of Gearman App functionality
     *
     */
    public function mainAction()
    {
        $server = (isset($this->config->gearman->server))
            ? $this->config->gearman->server
            : "";

        $worker = new GearmanWorker();
        $worker->addServer($server);
        $worker->addFunction("reverse", array($this, "reverse_fn"));
        foreach ($this->functions as $gearmanFuncName => $params) {
            $worker->addFunction($gearmanFuncName, $params);
        }

        while (1) {
            print "Waiting for job...\n";
            $ret = $worker->work();
            if ($worker->returnCode() != GEARMAN_SUCCESS)
                break;
        }

    }

    function reverse_fn($job)
    {
        $workload = $job->workload();
        echo "Received
                    job: " . $job->handle() . "\n";
        echo "Workload: $workload\n";
        $result = strrev($workload);
        /*
        for ($i = 1; $i <= 10; $i++) {
            $job->status($i, 10);
            sleep(1);
        }
        */
        echo "Result: $result\n";
        return $result;
    }

    public function startAction()
    {
        $this->mainAction();
    }

    public function restartAction()
    {

    }

    public function stopAction()
    {

    }

}