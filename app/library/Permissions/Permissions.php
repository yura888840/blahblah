<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 30.09.15
 * Time: 17:45
 */

namespace Crm\Permissions;

use Crm\Admin\Controllers\ControllerBase;

class Permissions implements IPermissions
{
    /**
     * Stub function
     * @param $object ControllerBase
     * @return mixed
     */
    public function getPermissions($object)
    {
        return $object->permissions;
    }
}