<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 26.05.15
 * Time: 18:51
 */

namespace Crm\Helpers;


class UsersHelper
{

    public static function getPairsUsernameWithId()
    {
        $users = \Crm\Models\Users::find([['status' => 'Active']]);

        $p = [];

        foreach ($users as $user) {
            $p[strval($user->_id)] = $user->name;
        }

        return $p;
    }

    public static function getEmailById($id)
    {
        $u = \Crm\Models\Users::findById($id);

        return $u->email;
    }

    public static function getPairsUsernameWithIdByIds(array $ids = [])
    {
        $users = [];

        foreach ($ids as $id) {
            $user = \Crm\Models\Users::findById($id);
            if ($user) {
                $users[] = $user;
            }
        }

        $p = [];

        foreach ($users as $user) {
            $p[strval($user->_id)] = $user->name;
        }

        return $p;
    }

    public static function getEmailsByUserIds(array $ids)
    {
        array_walk($ids, function ($v, $k) use (&$ids) {
            $ids[$k] = new \MongoId($v);
        });

        $users = \Crm\Models\Users::find([['_id' => ['$in' => $ids]]]);

        $emails = [];
        foreach ($users as $usr) {
            $emails[] = $usr->email;
        }

        return $emails;
    }

    public static function getUsersWthEmailsByUserIds(array $ids)
    {
        array_walk($ids, function ($v, $k) use (&$ids) {
            $ids[$k] = new \MongoId($v);
        });

        $users = \Crm\Models\Users::find([['_id' => ['$in' => $ids]]]);

        $emails = [];
        foreach ($users as $usr) {
            $emails[] = (!empty($usr->name) ? "<" . $usr->name . ">" : '') . $usr->email;
        }

        return $emails;
    }

    public static function getCurrentUserId()
    {
        $identity = \Phalcon\DI::getDefault()->get('auth')->getIdentity();
        return $identity['id'];
    }

}