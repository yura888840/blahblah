<?php

/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 27.08.15
 * Time: 16:12
 */
class RedisTestsTask extends \Phalcon\CLI\Task
{
    public function mainAction()
    {
        \Crm\tests\RedisCacheTest::bulk();
    }

    public function functionsAction()
    {
        \Crm\tests\RedisCacheTest::functionsTest();
    }

}