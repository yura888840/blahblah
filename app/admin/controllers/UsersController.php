<?php
namespace Crm\Admin\Controllers;

use Phalcon\Tag;
use Phalcon\Filter;
use Crm\Models\Users;
use Crm\Admin\Forms\AddUserForm;
use Crm\Admin\Forms\EditUserForm;
use Crm\Helpers\DateTimeFormatter;
use Crm\Helpers\AjaxAdd as AjaxAdd;
use Crm\Helpers\AjaxTablesorter;
use Crm\Helpers\DeleteHelper;

class UsersController extends ControllerBaseAdmin
{
    public function indexAction()
    {
        $form = new AddUserForm();

        $this->view->form = $form;

        $form = new EditUserForm();

        $this->forms->set('edit', $form);

    }

    public function ajaxTablesorterUserAction(){
        $this->view->disable();
        $request = new \Phalcon\Http\Request();

        if($request->isGet() == true && $request->isAjax() == true){

            $dbSelectors = ['&nbsp', 'name', 'email', 'profile', 'status', 'created', '&nbsp&nbsp'];

            $receive = $request->getQuery();
            $limit = $receive['size'];
            $skip = $limit * $receive['page'];
            $sort = $receive['column'];
            $filters = $receive['filter'];

            $tablesorter= new AjaxTablesorter();
            $dbSort = $tablesorter -> createDbSort($sort, $dbSelectors);
            $dbFilters = $tablesorter -> createMongoDbFilters($filters, $dbSelectors);

            $dbData = Users::Find(
                array(
                    $dbFilters,
                    "sort" => $dbSort,
                    "skip" => $skip,
                    "limit" => $limit,
                )
            );

            $tablesorter->headers = ['&nbsp', 'Name', 'E-mail', 'Profile', 'Status', 'Last Modify', '&nbsp&nbsp'];
            $tablesorter -> total_rows =  Users::Count([$dbFilters]);

            $tablesorter -> rows = $tablesorter -> tdStandartWrapper($dbData, $tablesorter -> headers, $dbSelectors, 'Name', 'Last Modify');

            foreach($tablesorter -> rows as $k => $v){
                foreach($v as $k2 => $v2){
                    if($k2 == '&nbsp&nbsp'){
                        $tablesorter -> rows[$k][$k2] = '<div class="not-touch-me text-right text-nowrap">'
                            . '<a class="btn btn-sm" ajax-tablesorter-element-id="'.$dbData[$k] -> _id.'" id="ajax-edit-tablesorter"><i class="glyphicon glyphicon glyphicon-pencil"></i></a>
                            <a class="btn btn-sm"  ajax-tablesorter-element-id="' . $dbData[$k]->_id . '"  id="ajax-delete-tablesorter"><i class="glyphicon glyphicon glyphicon-remove"></i></a>
                            <a class="btn btn-sm"  href="/admin/widgets/defaultgrid/' . $dbData[$k]->profile . '" alt="Widgets grid for group of the user"><i class="glyphicon glyphicon glyphicon-th-large"></i></a>'
                            . '</div>';
                    }
                }
            }

            echo json_encode($tablesorter);
        }
    }

    public function ajaxGetFilterOptionsAction(){
        $this->view->disable();
        $form = new EditUserForm();
        AjaxTablesorter::ajaxTableSelectOptions($form, [3 => 'profile', 4 => 'status']);
    }

    public function ajaxAddUserAction(){

        $this->view->disable();
        $request = new \Phalcon\Http\Request();
        $model = new Users();
        $form = new AddUserForm();

        if($request->isPost() == true && $request->isAjax() == true) {
            if (!AjaxAdd::ajaxSave($model, $form, $request)) {
                if(empty(Users::find([['email' => $this->request->getPost('email', 'email')]] ))) {
                    $model->password = $this->security->hash($this->request->getPost('password'));
                    if ($model->save()) {
                        $messagesArr['success'] = true;
                        echo json_encode($messagesArr);
                    }
                }
                else{
                    $messagesArr['email'] = 'This e-mail already exists';
                    echo json_encode($messagesArr);
                }
            }
        }
    }

    public function ajaxEditUserAction()
    {

        $this->view->disable();
        $request = new \Phalcon\Http\Request();

        $id = $request->getPost('_id');
        $model = Users::findById($id);

        if ($request->isPost() == true && $request->isAjax() == true && $model) {

            $form = new EditUserForm();

            if($request->getPost('password') &&
                ($request->getPost('password') == $request->getPost('confirmPassword')) )
            {
                $model->password = $this->security->hash($this->request->getPost('password'));
            }

            if ($this->config->admin->username == $this->auth->getName() && $model->email != $this->config->admin->username) {
                $model->profile = 'admin';
            }

            if (!AjaxAdd::ajaxSave($model, $form, $request)) {
                $model -> created = null;
                if ($model->save()) {

                    $this->rebuildUserDataIfCurrentUserEdited($model);

                    $messagesArr['success'] = true;
                    echo json_encode($messagesArr);
                }
            }
        }
    }

    private function rebuildUserDataIfCurrentUserEdited($model)
    {
        $currentUser = $this->auth->getName();

        $login = $model->email;

        if ($currentUser == $login) {
            $this->auth->remove();
            $this->auth->authUserById($model->_id);

            $userWidgets = \Crm\Models\Widgets::find([['user' => $model->email]]);

            foreach ($userWidgets as $w) {
                $w->delete();
            }
        }

        // rebuilding widgets set


    }

    public function ajaxDeleteUserAction(){
        $this->view->disable();
        $request = new \Phalcon\Http\Request();

        if($request->isPost() == true && $request->isAjax() == true) {
            $id = $request->getPost('_id');
            $data = Users::findById($id);

            DeleteHelper:: moveToTrash($data, 'users');
        }
    }

    public function ajaxGetUserAction(){
        $this->view->disable();
        $request = new \Phalcon\Http\Request();
        if($request->isPost() == true && $request->isAjax() == true) {
            $id = $request->getPost('id');
            $data = Users::findById($id);
            echo json_encode($data);
        }
    }

}

