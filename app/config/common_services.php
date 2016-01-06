<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 28.08.15
 * Time: 19:04
 */

use \Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;

/**
 * MongoDb connection
 */
$di->set('mongo', function () use ($config) {
//  $config->mongo->username=NULL;
    if (!$config->mongo->username OR !$config->mongo->password) {
        $mongo = new MongoClient('mongodb://' . $config->mongo->host);
    } else {
        $mongo = new MongoClient("mongodb://" . $config->mongo->username . ":" . $config->mongo->password . "@" . $config->mongo->host, array("db" => $config->mongo->database));
    }

    return $mongo->selectDb($config->mongo->database);
}, true);

/**
 * Redis cache connection
 *
 * @link https://docs.phalconphp.com/en/latest/api/Phalcon_Cache_Backend_Redis.html
 *
 * Example use cache:
 *
 * $di = \Phalcon\DI::getDefault();
 * $redisCache = $di->get('redisCache');
 *
 * $redisCache->save('my-data5', array(1, 2, 3, 4, 5));
 *
 * $data = $redisCache->get('my-data5');
 *
 * foreach ($redisCache->queryKeys($prefix.'my-d') as $key) {
 *  var_dump($key);
 * }
 *
 */

$di->setShared('redisCache', function () use ($config) {
    $frontCache = new \Phalcon\Cache\Frontend\Data(array(
        "lifetime" => $config['redis_lifetime']
    ));
    $cache = new \Phalcon\Cache\Backend\Redis($frontCache, array(
        'redis' => $config['redis'],
        'prefix' => $config['redis_prefix'],
    ));
    return $cache;
});

/**
 * GearmanClient
 *
 * Example add gearman Job
 * $res = \Phalcon\DI::getDefault()->get('gearmanClient')->doNormal("ExampleFunction", "Hello World!");
 *
 *  Example add gearman Job Background
 * $res = \Phalcon\DI::getDefault()->get('gearmanClient')->doBackground("ExampleFunction", "Hello World!");
 *
 */

$di->setShared('gearmanClient', function () use ($config) {
    $client = new GearmanClient();

    $client->addServer($config->gearman->host, $config->gearman->port);

    return $client;
});


//Relating for mail sending

$di->set('MailingQueueStorage', function () use ($config) {
    $frontCache = new \Phalcon\Cache\Frontend\Data(array(
        "lifetime" => $config['redis_lifetime']
    ));
    $cache = new \Phalcon\Cache\Backend\Redis($frontCache, array(
        'redis' => $config['redis'],
        'prefix' => $config['redis_prefix'],
    ));

    return $cache->get($config->mailing->MailingQueueStorage);
});

$di->set('Mailer', function () use ($config) {

    $mailer = new \Crm\Mail\SwiftMailer();

    $mailerWrapper = new \Crm\Components\Mailing\Mailer\MailerWrapper($mailer);

    return $mailerWrapper;
});

/**
 * Setting up the view component
 */
$di->set('view', function () use ($config) {

    $view = new View();

    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines(array(
        '.volt' => function ($view, $di) use ($config) {

            $volt = new VoltEngine($view, $di);

            $volt->setOptions(array(
                'compiledPath' => $config->application->cacheDir,
                'compiledSeparator' => '_',
                'stat' => true,
                'compileAlways' => $config->custom->voltCompileAlways,
            ));

            return $volt;
        },
        '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
    ));

    return $view;
}, true);

$di->set('simple_view', function () use ($config) {
    $view = new Phalcon\Mvc\View\Simple();
    $view->setViewsDir($config->application->viewsDir);
    $view->registerEngines(array(
        '.volt' => function ($view, $di) use ($config) {

            $volt = new VoltEngine($view, $di);

            $volt->setOptions(array(
                'compiledPath' => $config->application->cacheDir,
                'compiledSeparator' => '_',
                'stat' => true,
                'compileAlways' => true,//$config->custom->voltCompileAlways,
            ));

            return $volt;
        },
        '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
    ));
    return $view;
}, true);