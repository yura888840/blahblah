<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 30.09.15
 * Time: 17:44
 */

namespace Crm\Permissions;


interface IPermissions
{

    public function getPermissions($object);
}