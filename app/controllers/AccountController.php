<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 21.05.15
 * Time: 14:09
 */

use Crm\Forms\ChangePasswordForm;
use Phalcon\Tag;

class AccountController extends ControllerBase
{
    public function indexAction()
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

    public function supportAction()
    {

    }

    public function widgetAction()
    {

    }
}