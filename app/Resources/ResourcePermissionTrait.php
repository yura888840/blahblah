<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 26.03.15
 * Time: 12:36
 */

namespace Crm\Resources;

use Crm\Auth\Auth;

use Crm\BinaryAccessRules;

trait ResourcePermissionTrait {

    public function checkAccess()
    {
        $identity = $this->getDI()->get('auth')->getIdentity();

        $profile = $identity['profile'];

        $id = $identity['id'];

        $this->getObjectPermission($profile, $id);

    }

} 