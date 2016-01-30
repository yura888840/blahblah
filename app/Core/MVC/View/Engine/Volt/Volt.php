<?php

/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 29.01.16
 * Time: 15:27
 */

namespace App\MVC\View\Engine;

class Volt extends \Phalcon\Mvc\View\Engine\Volt
{

    public function initCompiler()
    {
        $compiler = $this->getCompiler();

        $compiler->addFunction('helper', function () {
            return '$this->helper';
        });

        $compiler->addFunction('substr', 'substr');

    }

}
