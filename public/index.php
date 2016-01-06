<?php

//@todo environment
error_reporting(E_ALL);
ini_set('display_errors', '1');

try {

    define('BASE_DIR', dirname(__DIR__));
    define('APP_DIR', BASE_DIR . '/app');

    /**
     * Read the configuration
     */
    $config = include __DIR__ . "/../app/config/config.php";

    /**
     * Read auto-loader
     */
    include __DIR__ . "/../app/config/loader.php";

    /**
     * Read services
     */
    include __DIR__ . "/../app/config/services.php";
    
    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);
    
    $application->registerModules(
        array(
            'admin' => array(
                'className' => 'Crm\Admin\Module',
                'path'      => __DIR__ . '/../app/admin/Module.php',
            ),
        )
    );

    //enable debug panel
    if (isset($_REQUEST['_url'])) {
        $urlCrm = $_REQUEST['_url'];
    } else {
        $urlCrm = '';
    }
    $subUrl = substr($urlCrm, 1, 5);
    if ($config->environment == 'development' &&  ($subUrl != 'admin' OR $subUrl == false) ) {
        $di['app'] = $application;
        (new Snowair\Debugbar\ServiceProvider())->start();
    }
    //enable debug panel END

    //@todo if response is json or xml
    echo $application->handle()->getContent();

} catch (\Exception $e) {
    echo $e->getMessage();
    $trace = $e->getTraceAsString();
    $trace = preg_replace("/\n/", '<br>',$trace);
    echo $trace;
}
