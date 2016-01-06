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
    'environment' => 'production',
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
    // Redis save time 2 days
    'redis_lifetime' => 172800,
    'redis_sse_prefix' => 'server_sent_event_',
    'application' => array(
        'controllersDir' => __DIR__ . '/../../app/controllers/',
        'modelsDir'      => __DIR__ . '/../../app/models/',
        'viewsDir'       => __DIR__ . '/../../app/views/',
        'pluginsDir'     => __DIR__ . '/../../app/plugins/',
        'libraryDir'     => __DIR__ . '/../../app/library/',
        'cacheDir'       => __DIR__ . '/../../app/cache/',
        'formsDir'       => __DIR__ . '/../../app/forms/',
        'widgetDir'      => __DIR__ . '/../../app/widget/',
        'resourcesDir'   => __DIR__ . '/../../app/Resources/',
        'filtersDir'     => __DIR__ . '/../../app/filters/',
        'helpersDir'     => __DIR__ . '/../../app/Helpers/',
        'modulesDir'     => __DIR__ . '/../../app/modules/',
        'attachmentsDir' => __DIR__ . '/../../mail/attachments/',
        'eventsDir' => __DIR__ . '/../../app/Events/',
        'testsDir' => __DIR__ . '/../../app/tests/',
        'schemasDir' => __DIR__ . '/../../app/Schemas/',
        'componentsDir' => __DIR__ . '/../../app/Components/',
        'listenersDir' => __DIR__ . '/../../app/Listeners/',
        'servicesDir' => __DIR__ . '/../../app/Services/',
        'coreDir' => __DIR__ . '/../../app/Core/',
    ),
    'tickets' => array(
        'attachmentsDir' => __DIR__ . '/../../public/uploads',
    ),
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
    'import' => array(
        'log_path' => __DIR__ . '/../../logs/',
        'cache_path' => __DIR__ . '/../../app/cache/',
    ),
    'importParams' => array(
        'order_date_start' => '2012-05-31',
        'order_all' => true,
        'order_day' => 2,
    ),
        'webSocket' => array(
        'domain' => 'crm-staging.ecomitize.com',
        'port' => '8888',
    ),
    'api' => array(
        'google' => array(
            'calendar' => array(
                'service_key_p12_path' =>  __DIR__.'/../instance_config/'.$configIni->api->google->calendar->service_key_p12
            ),
        )
    ),
    'mailing' => array(
        'MailingQueueStorage' => 'mailing_queue_storage',
    ),
    'permissions' => array(
        'check' => true,
        'availableRoles' => array(
            'admin',
            'user',
            'guest',
        ),
    ),
));

// Hack due to Phalcon bug on \Phalcon\Config merge method
$config = new \Phalcon\Config ( array_replace_recursive($config->toArray(), $configIni->toArray()) );

require_once "routes.php";

$localConfig = dirname(__FILE__) . '/config_local.php';
if (file_exists($localConfig)) {
    require $localConfig;
}
$config['redis_prefix'] = '_'.$config['application']['publicUrl'].'_';


return $config;
