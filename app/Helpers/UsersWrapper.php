<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 25.05.15
 * Time: 18:42
 */

namespace Crm\Helpers;

use \Crm\Models\Users;


class UsersWrapper
{

    public static function findActiveById(string $id)
    {
        $usr = Users::findFirst([['_id' => $id, 'active' => 'Y']]);

        // some post- processing

        return $usr;
    }

}