<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 24.02.15
 * Time: 15:22
 */

use Crm\Models\ResetPassword;

/**
 * UserControlController
 * Provides help to users to confirm their passwords or reset them
 */
class UserControlController extends ControllerBase {

    public function initialize()
    {
        if ($this->session->has('auth-identity')) {
            //$this->view->setTemplateBefore('private');
        }
    }

    public function indexAction()
    {

    }

    public function resetPasswordAction()
    {
        $code = $this->dispatcher->getParam('code');

        $resetPassword = ResetPassword::findFirst(array(
            array('code' => $code)
        ));

        if (!$resetPassword) {
            return $this->dispatcher->forward(array(
                'controller' => 'index',
                'action' => 'index'
            ));
        }

        if ($resetPassword->reset != 'N') {
            return $this->dispatcher->forward(array(
                'controller' => 'auth',
                'action' => 'login'
            ));
        }

        $resetPassword->reset = 'Y';

        /**
         * Change the confirmation to 'reset'
         */
        if (!$resetPassword->save()) {

            foreach ($resetPassword->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                'controller' => 'index',
                'action' => 'index'
            ));
        }

        /**
         * Identify the user in the application
         */

        $this->auth->authUserById($resetPassword->usersId);
        $this->flash->success('Please reset your password');

        return $this->dispatcher->forward(array(
            'controller' => 'users',
            'action' => 'changePassword'
        ));
    }
} 