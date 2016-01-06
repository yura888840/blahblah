<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 01.10.15
 * Time: 17:36
 */

namespace Crm\Permissions;

class PagesMasks
{
    public static $pageBelongsTo = [
        'admin' => 0b100,
        'user' => 0b110,
        'guest' => 0b111,
    ];

    public static function getMaskFor($role = NULL)
    {
        return array_key_exists($role, self::$pageBelongsTo) ? self::$pageBeчсlongsTo[$role] : NULL;
    }
}