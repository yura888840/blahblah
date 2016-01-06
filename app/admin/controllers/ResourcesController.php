<?php
namespace Crm\Admin\Controllers;

use Phalcon\Tag;
use Phalcon\Filter;
use Crm\Admin\Forms\EditRoleForm;
use Crm\Models\Users;

use Crm\Models\Profiles;
use Crm\Helpers\AjaxAdd as AjaxAdd;
use Crm\Helpers\AjaxTablesorter;
use Crm\Helpers\DeleteHelper;
use Crm\Models\WidgetsPermissions;
use Crm\Helpers\DateTimeFormatter;

use Crm\Models\PrivateResources;
use Crm\Models\Resources;
use Crm\Models\Permissions;

class ResourcesController extends ControllerBaseAdmin
{
    public function indexAction()
    {
        $roles = $this->getAllRoles();

        $this->view->users = $this->getUsersByRoles($roles['rolesPrepared']);
        $this->view->roles = $roles['rolesRaw'];
        $this->view->profiles = $roles['rolesPrepared'];


        $resources = $this->getResources();
        $this->view->resources = $resources['resources'];
        $this->view->actions = $resources['actions'];

        $roleForm = new EditRoleForm();
        $this->view->roleForm = $roleForm;

        $this->view->forms = $this->getForms();
    }

    //@todo глянуть на счет активных ролей
    private function getAllRoles()
    {
        $roles = Profiles::find([[]]);

        $preparedRoles = array();
        foreach($roles as $role)
        {
            $date = DateTimeFormatter::format($role->created);

            $preparedRoles[] = array(
                'name' => $role->name,
                'is_active' => $role->active,
                'id' => strval($role->_id),
                'last_modified' => $date['created'],
            );

            $existingRoles[] = $role->name;
        }

        $roles = [];

        foreach ($preparedRoles as $v)
        {
            $roles[$v['name']] = $v['id'];
        }

        return ['rolesPrepared' => $preparedRoles, 'rolesRaw' => $roles];
    }

    private function getUsersByRoles($roles)
    {
        $usersPlaceholder = [];
        foreach ($roles as $role) {
            $usersPlaceholder[$role['name']] = [];
        }

        $users = Users::find([[]]);
        foreach ($users as $user) {
            if (array_key_exists($user->profile, $usersPlaceholder))
                $usersPlaceholder[$user->profile][] = ['name' => $user->name,
                    'email' => $user->email,
                    'id' => $user->_id
                ];
        }

        return $usersPlaceholder;
    }

    private function getForms()
    {
        $formsPerm = Permissions::find([['type' => 'form']]);

        $forms = array();
        foreach ($formsPerm as $k => $v) {
            $name = $v->name;
            $perms = $v->permissions;

            //hack
            // @todo fix this !!!
            foreach ($perms as &$perm) {
                if (array_key_exists(0, $perm)) {
                    $perm = array([0]);
                }
            }

            $forms[$name] = $perms;
        }

        return $forms;
    }

    private function getResources()
    {

        $res = PrivateResources::find([[], ['name' => 1, 'operation' => 1,]]);

        $resources = [];
        foreach ($res as $v) {
            if (!array_key_exists($v->name, $resources)) {
                $resources[$v->name] = [];
            }
            if (!in_array($v->operation, $resources[$v->name])) {
                $resources[$v->name][] = $v->operation;
            }
        }

        $actions = [];
        array_walk($resources, function ($v, $k) use (&$actions) {
            $actions = array_unique(array_merge($actions, $v));
        });

        // hack
        array_walk($actions, function (&$v, $k) {
            $v = ucfirst($v);
        });
        $actions = array_unique($actions);

        $resources = array_keys($resources);

        return array('resources' => $resources, 'actions' => $actions);
    }


    public function updateAction()
    {
        $this->view->disable();

        $result = ['success'=> true, 'items' => []];

        //@todo sanitizing
        if($this->request->isAjax() && $this->request->isPost())
        {
            $request = $this->request->getPost();

            $success = true;
            $items = [];

            //parsing permissions
            foreach($request as $k => $v)
            {
                $resource = Permissions::findFirst([['name' => $k]]);

                if(empty($resource))
                {
                    $resource = new Permissions();
                    $resource->name = $k;
                }

                $resource->permissions = $v;

                if(!$resource->save())
                {
                    $items = [];
                    foreach ($resource->getMessages() as $message) {
                        $msgs[] = $message;
                    }

                    $success = false;
                }
                $acl = new \Crm\Acl\Acl();
                $acl->rebuild();

                $result = array('success' => $success, 'items' => $items);

            }

        }

        header('Content-type: application/json');
        echo json_encode($result);
    }

    public function permissionsAction()
    {
        $perms = WidgetsPermissions::find([[]]);

        $widgets = [];
        foreach ($perms as $p) {
            foreach ($p as $k => $v)
                $widgets[$p->name] = $p->permissions;
        }

        $this->view->setVar('widgets', $widgets);
    }

    public function ajaxTablesorterRoleAction(){
        $this->view->disable();
        $request = new \Phalcon\Http\Request();

        if($request->isGet() == true && $request->isAjax() == true){

            $dbSelectors = ['&nbsp', 'name', 'active', 'created', '&nbsp&nbsp'];

            $receive = $request->getQuery();
            $limit = $receive['size'];
            $skip = $limit * $receive['page'];
            $sort = $receive['column'];
            $filters = $receive['filter'];

            $tablesorter= new AjaxTablesorter();
            $dbSort = $tablesorter -> createDbSort($sort, $dbSelectors);
            $dbFilters = $tablesorter -> createMongoDbFilters($filters, $dbSelectors);

            $dbData = Profiles::Find(
                array(
                    $dbFilters,
                    "sort" => $dbSort,
                    "skip" => $skip,
                    "limit" => $limit,
                )
            );

            $tablesorter -> headers = ['&nbsp', 'Name of role', 'Active', 'Last Modify', '&nbsp&nbsp'];
            $tablesorter -> total_rows =  Profiles::Count([$dbFilters]);
            $tablesorter->rows = $tablesorter->tdStandartWrapper($dbData,
                $tablesorter->headers,
                $dbSelectors,
                'Name',
                'Last Modify');

            foreach($tablesorter -> rows as $k => $v){
                foreach($v as $k2 => $v2){
                    if($k2 == '&nbsp&nbsp'){
                        $tablesorter -> rows[$k][$k2] = '<div class="not-touch-me text-right text-nowrap">'
                            . '<a class="btn btn-sm" ajax-tablesorter-element-id="'
                            . $dbData[$k]->_id
                            . '" id="ajax-edit-tablesorter-role"><i class="glyphicon glyphicon glyphicon-pencil"></i></a>
                            <a class="btn btn-sm"  ajax-tablesorter-element-id="' . $dbData[$k]->_id
                            . '"  id="ajax-delete-tablesorter"><i class="glyphicon glyphicon glyphicon-remove"></i></a>'
                            . '</div>';
                    }
                }
            }

            header("Content-type: application/json");
            echo json_encode($tablesorter);
        }
    }

    public function ajaxGetFilterOptionsAction(){
        $this->view->disable();
        $form = new EditRoleForm();
        AjaxTablesorter::ajaxTableSelectOptions($form, [2 => 'active']);
    }

    public function ajaxAddRoleAction(){
        $this->view->disable();
        $request = new \Phalcon\Http\Request();
        $model = new Profiles();
        $form = new EditRoleForm();

        if($request->isPost() == true && $request->isAjax() == true) {
            if (!AjaxAdd::ajaxSave($model, $form, $request)) {
                if ($model->save()) {
                    $messagesArr['success'] = true;
                    echo json_encode($messagesArr);
                }
            }
        }
    }

    public function ajaxEditRoleAction()
    {
        $this->view->disable();
        $request = new \Phalcon\Http\Request();
        $id = $request->getPost('_id');
        $model = Profiles::findById($id);

        if($request->isPost() == true && $request->isAjax() == true) {

            $form = new EditRoleForm();

            if (!AjaxAdd::ajaxSave($model, $form, $request)) {
                $model -> created = null;
                if ($model->save()) {
                    $messagesArr['success'] = true;
                    echo json_encode($messagesArr);
                }
            }
        }
    }

    public function ajaxDeleteRoleAction(){
        $this->view->disable();
        $request = new \Phalcon\Http\Request();

        if($request->isPost() == true && $request->isAjax() == true) {
            $id = $request->getPost('_id');
            $data = Profiles::findById($id);

            DeleteHelper:: moveToTrash($data, 'roles');
        }
    }

    public function getResourcesCheckedAction()
    {
        $this->view->disable();
        $request = new \Phalcon\Http\Request();

        if($request->isPost() == true && $request->isAjax() == true) {

            $roleId = $request->getPost('roleId');
            $resource = $request->getPost('resource');// вот у нас есть ресурс

            if (!$roleId || !$resource) {
                throw new \Exception('No correct input given');
            }

            $role = $this->getRolenameById($roleId);

            $resourcePermissions = Permissions::find([[
                'name' => $resource,
            ]]);

            $operations = [];
            foreach ($resourcePermissions as $permission) {
                if (array_key_exists($permission->operation, $operations)) {
                    continue;
                }

                $operations[$permission->operation] = [];

                if (!array_key_exists('users', $permission->permissions)) {
                    $this->fixResourcePermissions($permission, 'users');
                }

                foreach ($permission->permissions['users'] as $usr => $v) {
                    if (is_array($usr)) {
                        continue;
                    }

                    $group = $this->getUserGroup($usr);

                    if ($group && ($group == $role)) {
                        $operations[$permission->operation][$usr] = $v;
                    }
                }

                if (!array_key_exists('groups', $permission->permissions)) {
                    $this->fixResourcePermissions($permission, 'groups');
                }

                if (array_key_exists($role, $permission->permissions['groups'])) {

                    $operations[$permission->operation][0] = $permission->permissions['groups'][$role];
                }
            }

            $out = [];
            foreach ($operations as $action => $v) {
                if (empty($v)) {
                    continue;
                }

                foreach ($v as $id => $permissionVal) {
                    $out[$id][$action] = $permissionVal;
                }
            }

            $response = [
                'resource' => $resource,
                'roleId' => $roleId,
                'success' => true,
                'users' => $out,
            ];

            header('Content-type: application/json');
            echo json_encode($response);
        }
    }

    private function fixResourcePermissions($p, $entityName)
    {
        $p->permissions[$entityName] = array();

        if (!$p->save()) {
            throw new \Exception('Error while updating permissions');
        }
    }


    private function getUserGroup($userId)
    {
        $u = Users::findById($userId);
        return $u->role;
    }

    private function getRolenameById($id)
    {
        $p = Profiles::findById($id);
        return $p->name;
    }
}
