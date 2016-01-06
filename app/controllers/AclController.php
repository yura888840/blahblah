<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 21.04.15
 * Time: 16:20
 */

use Crm\Models\Profiles;
use Crm\Models\DefaultPermissions;
use Crm\Models\Permissions;

class AclController extends ControllerBase
{
    /**
     * Function reload default permissions for all existing groups
     *
     */
    public function resetAction()
    {
        $profiles = Profiles::find([['active' => 'Y']]);

        $resources = DefaultPermissions::find([[]]);

        $c = Permissions::find([["type" => ['$exists' => false]]]);

        foreach ($c as $v) {
            $v->delete();
        }

        foreach ($profiles as $p) {
            foreach ($resources as $r) {
                $perm = new Permissions();

                $perm->profile = $p->name;
                $perm->resource = strtolower($r->resource);
                $perm->action = $r->action;

                if (!$perm->save()) {
                    throw new \Exception('Error while saving default permissions');
                }

            }
        }

        //  @todo back Up of perms
        $this->acl->rebuild();

        echo 'Success.';
    }

}