<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 04.03.15
 * Time: 13:13
 */

$configCLI = new Phalcon\Config(array(
    'mailfetcher' => array(
        'imap' => array(
            array(
                'host' => 'imap.gmail.com',
                'port' => 993,
                'username' => 'testcrmcrmtest',
                'password' => 'nbanbanbanba'
            ),
        ),

        'pop3' => array(
            array(
                'host' => '',
                'port' => '',
                'username' => '',
                'password' => ''
            ),
        ),
    ),
));

$localConfig = dirname(__FILE__) . '/config.php';
if (file_exists($localConfig)) {
    require_once $localConfig;
}

$config->merge($configCLI);

return $config;