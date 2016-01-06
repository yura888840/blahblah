<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 15.09.15
 * Time: 14:11
 */

namespace Crm\Admin\Controllers;

use Crm\Models\Users;

class PermissionsController extends ControllerBaseAdmin
{
    public function indexAction()
    {
        $permissions = \Crm\Models\SimplePermissions::find([[]]);

        $p = [];
        array_walk($permissions, function (&$v) use (&$p) {
            $v = $v->toArray();

            $p[$v['userId']] = $v['permisssions'];
        });

        $permissions = $p;
        $users = Users::find([['status' => 'Active']]);

        array_walk($users, function (&$v) use ($permissions) {
            $v = $v->toArray();
            $v['_id'] = strval($v['_id']);
            $v['id'] = $v['_id'];

            $v['permissions'] = [
                'google_calendar' => array_key_exists($v['_id'], $permissions)
                    ? $permissions[$v['_id']]['google_calendar']
                    : false,

                'ticket_assign' => array_key_exists($v['_id'], $permissions)
                    ? $permissions[$v['_id']]['ticket_assign']
                    : false,
            ];
        });

        $this->view->users = $users;
    }

}