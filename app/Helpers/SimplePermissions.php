<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 17.09.15
 * Time: 19:49
 */

namespace Crm\Helpers;


class SimplePermissions
{

    public static function canAssign()
    {
        $di = \Phalcon\DI::getDefault();

        $identity = $di->get('auth')->getIdentity();

        $userId = strval($identity['id']);

        $permissionGoogleCalendar = false;
        $permissionTicketAssign = false;

        $simplePermissions = \Crm\Models\SimplePermissions::find([['userId' => $userId]]);

        if (!empty($simplePermissions)) {
            $perms = $simplePermissions[0]->permisssions;

            $permissionGoogleCalendar = $perms['google_calendar'];
            $permissionTicketAssign = $perms['ticket_assign'];

        }

        return $permissionTicketAssign;
    }

}