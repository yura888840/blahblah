<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    array(
        $config->application->controllersDir,
        $config->application->modelsDir
    )
);

$loader->registerNamespaces(
    array(
        'Crm' => $config->application->libraryDir,
        'Crm\Models' => $config->application->modelsDir,
        'Crm\Forms' => $config->application->formsDir,
        'Crm\Widget' => $config->application->widgetDir,
        'Crm\Filters' => $config->application->filtersDir,
        'Crm\Helpers' => $config->application->helpersDir,
        'Crm\Resources' => $config->application->resourcesDir,
        'Crm\Modules' => $config->application->modulesDir,
        'Crm\Events' => $config->application->eventsDir,
        'Crm\tests' => $config->application->testsDir,
        'Crm\Schemas' => $config->application->schemasDir,
        'Crm\Components' => $config->application->componentsDir,
        'Crm\Listeners' => $config->application->listenersDir,
        'Crm\Services' => $config->application->servicesDir,
        'Crm\Core' => $config->application->coreDir,
    )
);

$loader->register();

require_once __DIR__ . '/../../vendor/autoload.php';
