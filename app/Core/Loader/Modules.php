<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 18.01.16
 * Time: 22:01
 */

namespace App\Loader;

use Phalcon\Text;

class Modules
{

    public function modulesConfig($modules_list)
    {
        $namespaces = array();
        $modules = array();

        if (!empty($modules_list)) {
            foreach ($modules_list as $module) {
                $namespaces[$module] = APPLICATION_PATH . '/modules/' . $module;
                $simple = Text::uncamelize($module);
                $simple = str_replace('_', '-', $simple);
                $modules[$simple] = array(
                    'className' => $module . '\Module',
                    'path' => APPLICATION_PATH . '/modules/' . $module . '/Module.php'
                );
            }
        }
        $modules_array = array(
            'loader' => array(
                'namespaces' => $namespaces,
            ),
            'modules' => $modules,
        );

        return $modules_array;
    }

}