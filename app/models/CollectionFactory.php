<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 04.05.15
 * Time: 18:28
 */

namespace Crm\Models;


class CollectionFactory
{

    const WIDGETS_NAMESPACE = 'Crm\\Models\\';

    public static function getNewInstanceOf($collectionName, $params)
    {
        $fullClassName = self::WIDGETS_NAMESPACE . $collectionName;

        if (!class_exists($fullClassName)) {
            throw new \Exception('Trying instantiate non- existing collection');
        }

        if (!is_array($params)) {
            throw new \Exception('Params must be of the type array');
        }

        $obj = new $fullClassName();

        foreach ($params as $k => $v) {
            if (property_exists($obj, $k)) {
                $obj->$k = $v;
            }
        }

        return $obj;

    }


}