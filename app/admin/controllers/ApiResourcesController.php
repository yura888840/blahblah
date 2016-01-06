<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 16.07.15
 * Time: 13:37
 */

namespace Crm\Admin\Controllers;

/**
 * Class ApiResourcesController
 * @package Crm\Admin\Controllers
 * @module(Admin\Api\Resources)
 */
class ApiResourcesController extends ControllerBaseApi
{
    /**
     * List of available entities
     *
     * @permission({permission_group="list", default="deny"})
     * @acl(true)
     */
    public function indexAction()
    {
        echo 'API end- point';
    }

    public function createAction()
    {

    }

    /**
     * Отдача
     * @throws \Exception
     */
    public function setCheckboxesChangesAction()
    {

        $this->view->disable();
        $request = new \Phalcon\Http\Request();

        if ($request->isPost() == true && $request->isAjax() == true) {
            /**
             * data :
             *  [resource][userId][action] = 0 или 1
             */

            $messagesArr = [];

            $data = $request->getPost();
            // @todo добавить проверки входных параметров

            foreach ($data as $resourceData) {

                foreach ($resourceData as $resourceName => $inPermissions) {

                    // @todo fix Db integrity
                    $installedModule = \Crm\Models\Resources::findFirst([[
                        'resource' => $resourceName,
                    ]]);

                    if ($installedModule) {
                        $operations = $installedModule->operations;
                    } else {
                        throw new \Exception('Module is not present');
                        //@todo autoinstall of module
                    }

                    foreach ($operations as $k => $v) {
                        unset($operations[$k]);
                        $operations[strtolower($k)] = $v;
                    }

                    $availOps = array_keys($operations);

                    foreach ($inPermissions as $id => $permissionData) {

                        foreach ($permissionData as $permissionName => $v) {

                            $permissionName = strtolower($permissionName);

                            if (!in_array($permissionName, $availOps)) {
                                continue;
                            }

                            $permissionFromDb = \Crm\Models\Permissions::find(
                                [[
                                    'name' => $resourceName,
                                    'operation' => $permissionName,
                                ]]
                            );

                            if (!$permissionFromDb) {
                                continue;
                            }

                            $messagesArr = array_merge($messagesArr,
                                $this->processPermissions($permissionFromDb, $id, $v));
                        }
                    }
                }

            }

            $messagesArr['success'] = true;
            header('Content-type: application/json');
            echo json_encode($messagesArr);
        }
    }

    public function setCheckboxesChangesPermsAction()
    {
        $this->view->disable();
        $request = new \Phalcon\Http\Request();

        if ($request->isPost() == true && $request->isAjax() == true) {
            $data = $request->getPost();

            foreach ($data as $v) {

                $userPerms = [];
                foreach ($v['common'] as $userId => $perm) {
                    $permissionName = implode('', array_keys($perm));

                    $permissionValue = implode('', array_values($perm));

                    //echo 'User : ' . $userId . ' ' . $permissionName . ' ' . $permissionValue . PHP_EOL;
                    $userPerms[$userId][$permissionName] = $permissionValue;
                }

                foreach ($userPerms as $userId => $perms) {
                    $permission = \Crm\Models\SimplePermissions::findFirst([['userId' => $userId]]);
                    if (!$permission) {
                        $permission = new \Crm\Models\SimplePermissions;
                        $permission->userId = $userId;
                    }

                    foreach ($perms as $permissionName => $permissionValue) {
                        $permission->permisssions[$permissionName] = ($permissionValue == "0") ? false : true;
                    }


                    if (!$permission->save()) {
                        throw new \Exception('Error while saving');
                    }
                }

            }
        }

        return true;
    }

    private function processPermissions($perms, $id, $v)
    {
        $messagesArr = [];
        foreach ($perms as $permission) {

            $id1 = $id;
            if (strpos($id, 'group_') !== false) {
                $entityName = 'groups';
                $id1 = substr($id, 6);
            } else {
                $entityName = 'users';
                $id1 = $id;
            }

            $this->saveEntityPermission($permission, $entityName, $id1, $v);
        }
        return $messagesArr;
    }

    private function saveEntityPermission($permission, $entityName, $id, $v)
    {
        if (!array_key_exists($entityName, $permission->permissions)
            || !is_array($permission->permissions[$entityName])
        ) {
            $permission->permissions[$entityName] = array();
        }

        if (!array_key_exists($id, $permission->permissions[$entityName])) {
            $permission->permissions[$entityName][$id] = 0;
        }

        $permission->permissions[$entityName][$id] = $v;

        if (get_class($permission) != 'Crm\Models\Permissions' || !method_exists($permission, 'save')) {
            throw new \Exception('Something wrong with model');
        }

        if (!$permission->save()) {
            throw new \Exception('Error while updating permissions');
        }
    }
}