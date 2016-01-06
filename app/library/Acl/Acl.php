<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 20.02.15
 * Time: 11:13
 */

namespace Crm\Acl;

use Phalcon\Mvc\User\Component;
use Phalcon\Acl\Adapter\Memory as AclMemory;
use Phalcon\Acl\Role as AclRole;
use Phalcon\Acl\Resource as AclResource;
use Crm\Models\Profiles;
use Crm\Models\Permissions;
use Crm\Models\PrivateResources;


/**
 * Access Control List class
 */
class Acl {

    /**
     * The ACL Object
     *
     * @var \Phalcon\Acl\Adapter\Memory
     */
    private $acl;

    /**
     * The filepath of the ACL cache file from APP_DIR
     *
     * @var string
     */
    private $filePath = '/cache/acl/data.txt';

    /**
     * Define the resources that are considered "private". These controller => actions require authentication.
     *
     * @var array
     */

    // @todo loading this resources from db
    private $privateResources = array(/*'dashboard' => array(
            'index'
        ),*/
    );

    //default right s

    private function loadPrivateResources()
    {
        $resources = PrivateResources::find([[]]);

        $privateResources = [];
        foreach ($resources as $v) {
            $privateResources[strtolower($v->resource)][] = $v->action;
        }

        // Get resources from table
        $tableResources = AclTable::getData();

        $this->privateResources = array_merge_recursive(
            array_merge_recursive($this->privateResources, $privateResources),
            $tableResources
        );

    }

    //@todo caching
    public function __construct()
    {
        $this->loadPrivateResources();
    }

    /**
     * Checks if a controller is private or not
     *
     * @param string $controllerName
     * @return boolean
     */
    // эта функция сейчас не используется, нигде
    public function isPrivate($controllerName)
    {
        return isset($this->privateResources[$controllerName]);
    }

    /**
     * Checks if the current profile is allowed to access a resource
     * P.S. profile also can be user
     *
     * @param string $profile
     * @param string $controller
     * @param string $action
     * @return boolean
     */
    public function isAllowed($profile, $controller, $action)
    {
        return $this->getAcl()->isAllowed($profile, $controller, $action);
    }

    /**
     * Returns the ACL list
     *
     * @return Phalcon\Acl\Adapter\Memory
     */
    public function getAcl()
    {
        // Check if the ACL is already created
        if (is_object($this->acl)) {
            return $this->acl;
        }

        // Check if the ACL is in APC
        if (function_exists('_apc_fetch')) {
            $acl = apc_fetch('crm-acl');
            if (is_object($acl)) {
                $this->acl = $acl;
                return $acl;
            }
        }

        // Check if the ACL is already generated
        if (!file_exists(APP_DIR . $this->filePath)) {
            $this->acl = $this->rebuild();
            return $this->acl;
        }

        // Get the ACL from the data file
        $data = file_get_contents(APP_DIR . $this->filePath);
        $this->acl = unserialize($data);

        // Store the ACL in APC
        if (function_exists('apc_store')) {
            apc_store('crm-acl', $this->acl);
        }

        return $this->acl;
    }

    /**
     * Rebuilds the access list into a file
     *
     * @return \Phalcon\Acl\Adapter\Memory
     */
    public function rebuild()
    {
        $acl = new AclMemory();

        // вот тут - продумать  ..
        $acl->setDefaultAction(\Phalcon\Acl::DENY);

        $profiles = Profiles::find(array(
            array('active' => 'Y')
        ));

        foreach ($profiles as $profile) {
            $acl->addRole(new AclRole($profile->name));
        }

        foreach ($this->privateResources as $resource => $actions) {
            $acl->addResource(new AclResource($resource), $actions);
        }

        // Grant acess to private area to role Users
        foreach ($profiles as $profile) {

            $permissions = Permissions::find(array(
                array('profile' => $profile->name)
            ));

            foreach ($permissions as $permission) {
                $acl->allow($profile->name, $permission->resource, $permission->action);
            }
        }


        $acl->setDefaultAction(\Phalcon\Acl::DENY);

        $roles = Profiles::find([['active' => 'Y']]);
        foreach($roles as $role)
        {
            $acl->addRole(new AclRole($role->name));
        }

        //Setting Up Forms
        $permissions = Permissions::find([['type' => 'form']]);

        foreach($permissions as $k => $v)
        {

            //@todo move this, may be to module dictionary
            $actions = array('show', 'canCustomize', 'otherPerm');

            $acl->addResource(new AclResource($v->name), $actions);

            foreach($v->permissions as $role => $permList)
            {
                foreach($permList as $kPerm => $realPerm)
                {
                    $act = $actions[$kPerm];
                    $aclAD = $realPerm ? 'allow' : 'deny';

                    $acl->{$aclAD}($role, $v->name, $act);
                }
            }
        }

        if (touch(APP_DIR . $this->filePath) && is_writable(APP_DIR . $this->filePath)) {

            file_put_contents(APP_DIR . $this->filePath, serialize($acl));

            // Store the ACL in APC
            if (function_exists('apc_store')) {
                apc_store('crm-acl', $acl);
            }
        } else {
            $this->flash->error(
                'The user does not have write permissions to create the ACL list at ' . APP_DIR . $this->filePath
            );
        }

        return $acl;
    }


} 