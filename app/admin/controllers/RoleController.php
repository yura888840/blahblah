<?php

namespace Crm\Admin\Controllers;
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Crm\Models\Role;

class RoleController extends ControllerBaseAdmin
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * Searches for role
     */
    public function searchAction()
    {

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = array();
        }
        $parameters["order"] = "id";

        $roles = Role::find(array());
        if (count($roles) == 0) {
            $this->flash->notice("The search did not find any role");

            return $this->dispatcher->forward(array(
                "module" => "admin",
                "controller" => "role",
                "action" => "index"
            ));
        }

        $_roles = array();
        foreach($roles as $k => $role)
        {
            $roles[$k] = $role->toArray();

            $roles[$k]['_id'] = strval($roles[$k]['_id']);
        }

        $this->view->setVar('roles', $roles);
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {

    }

    /**
     * Edits a role
     *
     * @param string $id
     */
    public function editAction($id)
    {

        if (!$this->request->isPost()) {

            $role = Role::findById($id);

            if (!$role) {
                $this->flash->error("role was not found");

                return $this->dispatcher->forward(array(
                    "module" => "admin",
                    "controller" => "role",
                    "action" => "index"
                ));
            }

            $role = $role->toArray();
            $role['_id'] = strval($role['_id']);

            $this->view->setVar('id', $role['_id']);
            $this->view->setVar('name', $role['name']);
        }
    }

    /**
     * Creates a new role
     */
    //@todo load default template permissions for role
    public function createAction()
    {
        if (!$this->request->isPost()) {

        } elseif(empty($name = $this->request->getPost("name"))) {
            $this->flash->error("role name can't be empty");
        } elseif($isExists = !empty(Role::find([['name' => $name]]))) {
            $this->flash->error("role already exists");
        } else {
            $role = new Role();

            $role->name = $name;

            if (!$role->save()) {
                foreach ($role->getMessages() as $message) {
                    $this->flash->error($message);
                }

                return $this->dispatcher->forward(array(
                    "module" => "admin",
                    "controller" => "role",
                    "action" => "new"
                ));
            }

            $this->flash->success("role was created successfully");
        }

        return $this->dispatcher->forward(array(
            "module" => "admin",
            "controller" => "role",
            "action" => "index"
        ));
    }

    /**
     * Saves a role edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "role",
                "action" => "index"
            ));
        }

        $id = $this->request->getPost("id");

        $role = Role::findById($id);

        if (!$role) {
            $this->flash->error("role does not exist " . $id);

            return $this->dispatcher->forward(array(
                "controller" => "role",
                "action" => "index"
            ));
        }

        //@todo filters
        $role->name = $this->request->getPost("name");

        if (!$role->save()) {

            foreach ($role->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "module" => "admin",
                "controller" => "role",
                "action" => "edit",
                "params" => array($role->_id)
            ));
        }

        $this->flash->success("role was updated successfully");

        return $this->dispatcher->forward(array(
            "module" => "admin",
            "controller" => "role",
            "action" => "index"
        ));

    }

    /**
     * Deletes a role
     *
     * @param string $id
     */
    public function deleteAction($id)
    {

        $role = Role::findByid($id);
        if (!$role) {
            $this->flash->error("role was not found");

            return $this->dispatcher->forward(array(
                "module" => "admin",
                "controller" => "role",
                "action" => "index"
            ));
        }

        if (!$role->delete()) {

            foreach ($role->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "module" => "admin",
                "controller" => "role",
                "action" => "search"
            ));
        }

        $this->flash->success("role was deleted successfully");

        return $this->dispatcher->forward(array(
            "module" => "admin",
            "controller" => "role",
            "action" => "index"
        ));
    }

    public function getListAction()
    {
        $this->view->disable();

        if(!$this->request->isAjax())
        {
            return $this->dispatcher->forward(array(
                "module" => "admin",
                "controller" => "role",
                "action" => "index"
            ));
        }

        $roles = Role::find();
        array_walk($roles, function(&$v, $k) {$v = $v->toArray();$v = $v['name'];});

        header('Content-type: application/json');
        echo json_encode(['success' => true, 'items' => $roles]);

    }

}
