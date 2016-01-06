<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 12.11.15
 * Time: 20:00
 */

namespace Crm\Helpers;


class MongoKeySupportHelper
{
    public static function directArrayTransform($data)
    {
        $functor = function ($v) {
            return str_replace(".", "\\u002e", $v);
        };
        return self::doTransformation($data, $functor);
    }

    public static function inverseArrayTransform($data)
    {
        $functor = function ($v) {
            return str_replace("\\u002e", ".", $v);
        };
        return self::doTransformation($data, $functor);
    }

    private static function doTransformation($data, $functor)
    {
        foreach ($data as $k => $v) {
            $k1 = $k;

            $k = $functor($k);
            $v = $functor($v);

            $data[$k] = $v;
            unset($data[$k1]);
        }

        return $data;
    }

    public static function directTransform($key)
    {
        $transformed = str_replace($key, ".", "\\u002e");

        return $transformed;
    }

    public static function inverseTransform($key)
    {
        $transformed = str_replace($key, "\\u002e", ".");

        return $transformed;
    }
}