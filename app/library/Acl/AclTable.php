<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 29.09.15
 * Time: 16:14
 */

namespace Crm\Acl;

class AclTable
{

    /**
     * Fixed table with private resources
     *
     * @var array
     */
    private static $table = [
        'dashboard' => array(
            'index'
        ),
    ];

    public static function getData()
    {
        return self::$table;
    }

}