<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 01.10.15
 * Time: 15:07
 */

namespace Crm\Permissions;

class RolesMasks
{
    public static $binaryMask = [
        'admin' => 0b111,
        'user' => 0b011,
        'guest' => 0b001,
    ];

    public static function getMaskFor($role)
    {
        return array_key_exists($role, self::$binaryMask) ? self::$binaryMask[$role] : NULL;
    }
}