<?php

try {
    $configIni = new \Phalcon\Config\Adapter\Ini(__DIR__ . '/../instance_config/config.ini');
} catch (Exception $e) {
    if(0 == $e->getCode())
    {
        die('Error parsing app/instance_config/config.ini . Please check if file exists or syntax correctness. Exiting..');
    }
}

$config = new \Phalcon\Config(array(
    'application' => array(
        'appDir' => __DIR__ . '/../../app/Core/',
    ),

    'database' => array(
        'adapter'     => 'Mysql',
        'host'        => 'localhost',
        'username'    => 'root',
        'password'    => '',
        'dbname'      => 'phalcon',
        'charset'     => 'utf8',
    ),
    'redis' => array(
        'host' => '127.0.0.1',
        'port' => 6379,
        'persistent' => true
    ),
    'gearman' => array(
        'host' => '127.0.0.1',
        'port' => 4730,
    ),
    'redis_lifetime' => 172800,
    'redis_sse_prefix' => 'server_sent_event_',
    'uploadFiles' => [
        'tmp' => __DIR__ . '/../../public/files/tmp',
    ],
    // leave this empty if project located in top of the path, e.g. http://myhost.com/
    'custom' => array(
        'root_path' => '',
        'assets_path' => '',
        'assets_path_tpl' => '/',
        'voltCompileAlways' => true,
    ),
    'api' => array(
        'google' => array(
            'calendar' => array(
                'service_key_p12_path' =>  __DIR__.'/../instance_config/',
            ),
        )
    ),
    'app' => [
        'DEBUG_MODE' => false,
        ]
));

$config = new \Phalcon\Config ( array_replace_recursive($config->toArray(), $configIni->toArray()) );

require_once __DIR__ . "/../Core/routes.php";

$config->merge($configRoutes);

$modules_list = include_once APPLICATION_PATH . '/config/modules.php';
require_once APPLICATION_PATH . '/Core/Loader/Modules.php';
$modules = new \App\Loader\Modules();
$modules_config = $modules->modulesConfig($modules_list);

$config = new \Phalcon\Config ( array_merge_recursive($config->toArray(), $modules_config, ['loader' => ['dirs'=>[]]]) );

$config['redis_prefix'] = '_'.$config['application']['publicUrl'].'_';

return $config;
