<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 24.02.15
 * Time: 16:56
 */

use Crm\Forms\ChangePasswordForm;
use Phalcon\Tag;

class UsersController extends ControllerBase
{

    public function initialize()
    {
        $this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_ACTION_VIEW);

    }

    public function changePasswordAction()
    {
        $this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_MAIN_LAYOUT);
        $form = new ChangePasswordForm();

        if ($this->request->isPost()) {

            if (!$form->isValid($this->request->getPost())) {

                foreach ($form->getMessages() as $message) {
                    $this->flash->error($message);
                }
            } else {

                try {
                    $user = $this->auth->getUser();
                } catch (\Exception $e) {
                    $this->flash->success('To change password you must be logged in');
                    /*return $this->dispatcher->redirect(array(
                        'controller' => 'auth',
                        'action' => 'login'
                    ));*/
                    return $this->response->redirect('dashboard');
                }


                $user->password = $this->security->hash($this->request->getPost('password'));
                $user->mustChangePassword = 'N';

                if (!$user->save()) {
                    $this->flash->error('Error while saving user');
                } else {

                    $this->flash->success('Your password was successfully changed');

                    Tag::resetInput();
                    return $this->response->redirect('dashboard');
                }
            }
        }

        $this->view->form = $form;
    }

} 