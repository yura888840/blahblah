<?php
define('ROOT', __DIR__);
define('HOST_HASH', substr(md5($_SERVER['HTTP_HOST']), 0, 12));

defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

define('APPLICATION_PATH', __DIR__ . '/../app');

//@todo environment error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

//require_once APPLICATION_PATH . '/Bootstrap.php';

include __DIR__ . "/public/bootstrap.php";
