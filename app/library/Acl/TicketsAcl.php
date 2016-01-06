<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 25.05.15
 * Time: 13:13
 */

namespace Crm\Acl;


class TicketsAcl
{

    public static function hasAccess(\Crm\Models\Tickets $ticket)
    {
        $authData = \Phalcon\DI::getDefault()->get('auth')->getIdentity();

        $access = false;

        if ($authData['profile'] == $ticket->editRoleRights()) {
            $access = true;
        } else {
            if ($authData['id'] == $ticket->assignTo) {
                $access = true;
            } else {
                foreach ($ticket->notify as $v) {
                    if ($authData['id'] == $v) {
                        $access = true;
                        break;
                    }
                }
            }
        }

        $access = true;

        return $access;
    }
}