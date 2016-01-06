<?php

use Phalcon\DI\FactoryDefault\CLI as CliDI,
    Phalcon\CLI\Console as ConsoleApp;

define('VERSION', '1.0.0');
define('IS_CONSOLE', true);

//Используем стандартный для CLI контейнер зависимостей
$di = new CliDI();
$s = $di->getServices();
//print_r($s); die();
// Определяем путь к каталогу приложений
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__)));



/**
 * Регистрируем автозагрузчик, и скажем ему, чтобы зарегистрировал каталог задач
 */
$loader = new \Phalcon\Loader();
$loader->registerDirs(
    array(
        APPLICATION_PATH . '/tasks'
    )
);
$loader->register();

// Загружаем файл конфигурации, если он есть
if(file_exists(APPLICATION_PATH . '/config/config_cli.php')) {
    $config = include APPLICATION_PATH . '/config/config_cli.php';
    $di->set('config', $config);
}

include APPLICATION_PATH . "/config/loader.php";

/**
 * MongoDb collection manager
 */
$di->set('collectionManager', function(){
    return new Phalcon\Mvc\Collection\Manager();
}, true);

// Common Services for DI Container

include APPLICATION_PATH . "/config/common_services.php";

//Создаем консольное приложение
$console = new ConsoleApp();
$console->setDI($di);

/**
 * Определяем консольные аргументы
 */
$arguments = array();
$params = array();

foreach($argv as $k => $arg) {
    if($k == 1) {
        $arguments['task'] = $arg;
    } elseif($k == 2) {
        $arguments['action'] = $arg;
    } elseif($k >= 3) {
        $params[] = $arg;
    }
}
if(count($params) > 0) {
    $arguments['params'] = $params;
}

// определяем глобальные константы для текущей задачи и действия
define('CURRENT_TASK', (isset($argv[1]) ? $argv[1] : null));
define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));

try {
    // обрабатываем входящие аргументы
    $console->handle($arguments);
}
catch (\Phalcon\Exception $e) {
    echo $e->getMessage();
    exit(255);
}