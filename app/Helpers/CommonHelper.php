<?php

namespace Crm\Helpers;

class CommonHelper{

    public static function getShortClassName($object){
        $modelName = explode('\\', get_class($object));
        return array_pop($modelName);
    }
}