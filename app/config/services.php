<?php

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Dispatcher as PhDispatcher;
use Phalcon\Forms\Manager as FormsManager;
use Phalcon\Filter;
use Crm\Filters\PriorityFilter;
use Crm\Filters\StatusFilter;
use Crm\Filters\OrderFilter;

use Crm\Auth\Auth;
use Crm\Acl\Acl;
use Crm\Mail\Mail;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * Register the global configuration as config
 */
$di->set('config', $config);

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
}, true);


/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function () use ($config) {
    return new DbAdapter(array(
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname,
        'charset' => $config->database->charset
    ));
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Start the session the first time some component request the session service
 */
$di->set('session', function () {
    $session = new SessionAdapter();

    if (session_id() == '' || !isset($_SESSION)) {
        $session->start();
    }

    return $session;
});

/**
 * Routes from config
 */
$di->set('router', function () use ($config) {

    $router = new Router(false);

    $router->notFound(array(
        "controller" => "index",
        "action" => "route404"
    ));

    $routes = $config->routes;

    foreach ($routes as $uri => $rout) {

        if(!array_key_exists('action', $rout->params))
        {
            $rout->params['action'] = 'index';
        }

        $a = array(
            'controller' => $rout->params['controller'],
            'action' => $rout->params['action'],
        );

        if (array_key_exists('params', $rout->params)) {
            $a['params'] = $rout->params['params'];
        }

        if (isset($rout->params['module'])) {
            $a['module'] = $rout->params['module'];
        }

        $route = $router->add($config->custom->root_path . $uri, $a);

        if (isset($rout->name)) {
            $route->setName($rout->name);
        }
    }

    $router->removeExtraSlashes(true);

    //$router->setDefaultModule("admin");

    return $router;
});

$di->set('dispatcher',
    function() use ($di) {

        $evManager = $di->getShared('eventsManager');

        // Плагин безопасности слушает события, инициированные диспетчером
        $evManager->attach('dispatch:beforeExecuteRoute', new \Crm\Permissions\SecurityPlugin());

        $evManager->attach(
            "dispatch:beforeException",
            function($event, $dispatcher, $exception)
            {
                switch ($exception->getCode()) {
                    case PhDispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                    case PhDispatcher::EXCEPTION_ACTION_NOT_FOUND:
                        $dispatcher->forward(
                            array(
                                'controller' => 'index',
                                'action'     => 'route404',
                            )

                        );
                        return false;
                }

                if ('Please login first' == $exception->getMessage()) {
                    $dispatcher->forward(
                        array(
                            'controller' => 'auth',
                            'action' => 'login',
                        )
                    );

                }
            }
        );
        $dispatcher = new PhDispatcher();
        $dispatcher->setEventsManager($evManager);
        return $dispatcher;
    },
    true
);

$di->set('flash', function () {
    $flash = new \Phalcon\Flash\Direct(array(
        'error' => 'text-danger',
        'success' => 'text-success',
        'notice' => 'text-info',
    ));
    return $flash;
});

$di->set('flashSession', function () {
    $flash = new \Phalcon\Flash\Session(array(
        'error' => 'text-danger',
        'success' => 'text-success',
        'notice' => 'text-info',
    ));
    return $flash;
});

/**
 * Service events Mongo Db
 */
$di->setShared('eventsMongo', function () {
    $eventsMongo = new \Crm\Events\Mongo();
    return $eventsMongo;
});

/**
 * MongoDb collection manager
 */
$di->set('collectionManager', function() {

    $eventsManager = new Phalcon\Events\Manager();

    // Attach an anonymous function as a listener for "model" events

    $eventsManager->attach('collection', function($event, $model) {
        $evManager = \Phalcon\DI::getDefault()->getShared('eventsMongo');

        $evManager->manager($event, $model);
        return true;
    });

    // Setting a default EventsManager
    $modelsManager = new Phalcon\Mvc\Collection\Manager();
    $modelsManager->setEventsManager($eventsManager);

    return $modelsManager;

}, true);

/**
 * Custom authentication component
 */
$di->set('auth', function () {
    return new Auth();
});

/**
 * Access Control List
 */
$di->set('acl', function () {
    return new Acl();
});

/**
 * Access Control List
 */
$di->setShared('aclCrm', function () {
    return new Phalcon\Acl\Adapter\Memory();
});

/**
 * Access Control List Admin Module
 */
$di->setShared('aclCrmAdmin', function () {
    return new Phalcon\Acl\Adapter\Memory();
});

/**
 * Access Control List DB Collection
 */
$di->setShared('aclCrmCollection', function () {
    return new Phalcon\Acl\Adapter\Memory();
});

/**
 * Mailer
 */
$di->set('mail', function () {
    return new Mail();
});

/**
 * Forms manager
 */
$di->set('forms', function () {
    return new FormsManager();
});

/**
 * Custom filters
 */
$di->set('filters', function () {
    $filter = new Filter();
    $filter->add('priority', new PriorityFilter());
    $filter->add('status', new StatusFilter());
    $filter->add('order', new OrderFilter());

    return $filter;
});

$di->set('crypt', function () use ($config) {
    $crypt = new Phalcon\Crypt();
    $crypt->setKey('b5aapm3V7kl78stb');
    return $crypt;
});

$di->set('component', array(
        'className' => '\Crm\Core\Component',
        'arguments' => array(
            array('type' => 'parameter', 'name' => 'component')
        )
    )
);

$di->set('dirConfig', function () {
    return  __DIR__;
});


require_once __DIR__ . "/common_services.php";