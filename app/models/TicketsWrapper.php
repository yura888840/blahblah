<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 14.09.15
 * Time: 12:53
 */

namespace Crm\Models;


class TicketsWrapper extends \Phalcon\Mvc\Collection
{
    protected $functionsStack = [

        // объединить функции в блок
    ];

    public static function find(array $parameters = NULL)
    {
        $a = static::chechPermissions();

        echo 'Permissions is:' . PHP_EOL . PHP_EOL;
        var_dump($a);

        return true;

        return parent::find($params);
    }

    public static function findOne($params)
    {

        return parent::findOne($params);
    }


    public static function chechPermissions()
    {
        $acl = new \Crm\Acl\Acl();
        $acl = $acl->getAcl();

        $identity = \Phalcon\DI::getDefault()->get('auth')->getIdentity();

        if ($identity) {
            $profile = $identity['profile'];
            $user = $identity['id'];
        } else {
            // Unauthorized
        }

        return $identity;

    }

}