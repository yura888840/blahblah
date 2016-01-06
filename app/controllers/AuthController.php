<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18.02.15
 * Time: 14:49
 */

use Crm\Forms\SignUpForm;
use Crm\Forms\ForgotPasswordForm;
use Crm\Models\Users;
use Crm\Models\ResetPassword;
use Phalcon\Tag;
use Crm\Forms\FormFactory;

class AuthController extends ControllerBase
{
    public function initialize()
    {
        $this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_ACTION_VIEW);

        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
    }

    public function loginAction()
    {
        if(is_array($this->auth->getIdentity()))
        {
            if ($this->cookies->has('url-redirect')) {
                $urlRedirect = trim($this->cookies->get('url-redirect')->getValue());
                $this->cookies->get('url-redirect')->delete();
                return $this->response->redirect($urlRedirect);
            }
            return $this->response->redirect('dashboard');
        }

        $this->view->setVar('login', '');
        $email = '';

        if($this->request->isPost() == true)
        {

            try {

                if ($this->request->isPost()) {
                        $email = $this->request->getPost('email');
                        $password = $this->request->getPost('password');
                        if(!empty($email) && !empty($password))
                        {
                            $this->auth->check(array(
                                'email' => $this->request->getPost('email'),
                                'password' => $this->request->getPost('password'),
                            ));
                        } else {
                            //$this->flash->success('Password change link was sent to your email');
                        }
                    if ($this->cookies->has('url-redirect')) {
                        $urlRedirect = trim($this->cookies->get('url-redirect')->getValue());
                        $this->cookies->get('url-redirect')->delete();
                        return $this->response->redirect($urlRedirect);
                    }
                    return $this->response->redirect('dashboard');
                }
            } catch (\Exception $e) {
                $this->view->setVar('login', $email);
                $this->flash->error($e->getMessage());
            }
        }
    }

    public function forgotPasswordAction()
    {
        if (is_array($this->auth->getIdentity())) {
            return $this->response->redirect('dashboard');
        }

        $form = new ForgotPasswordForm();

        $request = new Phalcon\Http\Request();
        if($request->isPost() == true) {

            $email = $request->getPost("email");

            $user = Users::findFirst(array(
                array('email' => $this->request->getPost('email'))
            ));

            if (!$user) {
                $this->flash->error('There is no account associated to this email');
            } else {

                $resetPassword = new ResetPassword();
                $resetPassword->createdAt = time();
                $resetPassword->code = preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(24)));
                $resetPassword->reset = 'N';
                $resetPassword->modifiedAt = time();
                $resetPassword->usersId = $user->_id;

                if ($resetPassword->save()) {
                    $this->getDI()->getMail()->send(array(
                        $user->email => $user->name )
                    , "Reset your password", 'reset', array(
                            'resetUrl' => 'reset-password/' . $resetPassword->code . '/' . $user->email,
                    ));

                    Tag::resetInput();
                    $this->flashSession->success('Success! Please check your messages for an email reset password');
                    return $this->dispatcher->forward( array(
                        'controller' => 'auth',
                        'action' => 'login'
                    ));
                } else {
                    foreach ($resetPassword->getMessages() as $message) {
                        $this->flash->error($message);
                    }
                }
            }

        }
        $this->view->form = $form;
    }

    /**
     * Shows the forgot password form
     */
    public function changePasswordAction()
    {

        if (is_array($this->auth->getIdentity())) {
            return $this->response->redirect('dashboard');
        }

        $form = FormFactory::getForm('ForgotPasswordForm');

        if ($this->request->isPost()) {

            if ($form->isValid($this->request->getPost()) == false) {
                foreach ($form->getMessages() as $message) {
                    $this->flash->error($message);
                }
            } else {

                $user = Users::findFirst(array(
                    array('email' => $this->request->getPost('email'))
                ));

                if (!$user) {
                    $this->flash->success('There is no account associated to this email');
                } else {

                }
            }
        }

        $this->view->form = $form;
    }

    public function passwordSentAction()
    {
        if (is_array($this->auth->getIdentity())) {
            return $this->response->redirect('dashboard');
        }

    }

    /**
     * Allow a user to signup to the system
     */
    public function registerAction()
    {

        if (!is_array($this->auth->getIdentity()) || !$this->auth->getIsAdmin()) {
            return $this->response->redirect('dashboard');
        }

        $form = FormFactory::getForm('SignUpForm');

        $this->view->form = $form;
        if ($this->request->isPost()) {

            if ($form->isValid($this->request->getPost()) != false
                && empty(Users::find([['email' => $this->request->getPost('email', 'email') ]] )) ) {

                $user = new Users();

                $user->name = $this->request->getPost('name', 'striptags');
                $user->email = $this->request->getPost('email', 'email');
                $user->password = $this->security->hash($this->request->getPost('password'));
                $user->profile = 'user';

                if ($user->save()) {
                    $this->flash->success('User created');
                    return $this->dispatcher->forward(array(
                        'controller' => 'index',
                        'action' => 'index'
                    ));
                }

                $this->flash->error($user->getMessages());

            } else $this->view->setVar('hasWarnings', true);
        }
        //@todo вынести в отдельную сущность
    }

    public function logoutAction()
    {
        $this->auth->remove();
        return $this->response->redirect('login');
    }

} 