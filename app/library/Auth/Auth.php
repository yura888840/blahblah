<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19.02.15
 * Time: 16:03
 */

namespace Crm\Auth;

use Phalcon\MVC\User\Component;
use Crm\Models\Users;

class Auth extends Component
{

    /**
     * Checks the user credentials
     *
     * @param array $credentials
     * @return boolan
     */
    public function check($credentials)
    {

        if (!$this->config->admin) {
            throw new \Exception('Error in config file - admin - section');
        }

        if ($credentials['email'] == $this->config->admin->username) {
            if ($credentials['password'] != $this->config->admin->password) {
                throw new \Exception('Wrong email/password combination');
            }

            $user = Users::findFirst(array(
                array(
                    'email' => $credentials['email'],
                )
            ));

            $this->session->set('auth-identity', array(
                'id' => $user->_id,
                'name' => $credentials['email'],
                'profile' => 'admin',
            ));
            return;
        }


        // Check if the user exist
        $user = Users::findFirst(array(
            array(
                'email' => $credentials['email'],
                )
        ));
        if ($user == false) {
            throw new \Exception('Wrong email/password combination');
        } elseif ($user->status != 'Active') {
            throw new \Exception('User is blocked');
        }

        if (!(isset($this->config->application->anyPassword) && $this->config->application->anyPassword)) {
            if(!$this->security->checkHash($credentials['password'], $user->password)) {
                throw new \Exception('Wrong email/password combination');
            }
        }

        $this->session->set('auth-identity', array(
            'id' => $user->_id,
            'name' => $user->email,
            'profile' => !empty($user->profile) ? $user->profile : 'guest',
        ));
    }

    /**
     * Returns the current identity
     *
     * @return array
     */
    public function getIdentity()
    {
        $credentials = $this->session->get('auth-identity');

        if (!$credentials) {
            return false;
        }

        $user = Users::findFirst(array(
            array(
                'email' => $credentials['name'],
            )
        ));

        if (!$user) {
            return false;
        }

        if ($user->status != 'Active') {
            throw new \Exception('User is blocked');
        }

        $credentials['profile'] = !empty($user->profile) ? $user->profile : 'guest';
        $credentials['google_calendar_id'] = !empty($user->google_calendar_id) ? $user->google_calendar_id : '';

        //@todo перенести все из коллекцию SimplePermissions в коллекцию Users
        $simplePermissions = \Crm\Models\SimplePermissions::findFirst([['userId' => (string)$credentials['id']]]);
        if (!empty($simplePermissions->permisssions['google_calendar'])) {
            $credentials['see_company_calendar'] = $simplePermissions->permisssions['google_calendar'];
        } else {
            $credentials['see_company_calendar'] = false;
        }

        return $credentials;
    }

    /**
     * Returns the current user identity
     *
     * @return string
     */
    public function getName()
    {
        $identity = $this->session->get('auth-identity');
        return $identity['name'];
    }

    /**
     * Returns the current profile identity
     *
     * @return string
     */
    public function getProfile()
    {
        $identity = $this->getIdentity();

        return $identity['profile'];
    }

    /**
     * Removes the user identity information from session
     */
    public function remove()
    {
        $this->session->remove('auth-identity');
    }

    /**
     * Auths the user by id
     *
     * @param string $id
     */
    public function authUserById($id)
    {
        $user = Users::findFirst(array(
            array( '_id' => $id)
        ));
        if ($user == false) {
            throw new Exception('The user does not exist');
        }

        $this->checkUserFlags($user);

        $this->session->set('auth-identity', array(
            'id' => $user->_id,
            'name' => $user->name,
            'profile' => !empty($user->profile) ? $user->profile : 'user',
        ));
    }

    /**
     * Checks if the user is banned/inactive/suspended
     *
     * @param Crm\Models\Users $user
     */
    public function checkUserFlags(Users $user)
    {

    }

    /**
     * Get the entity related to user in the active identity
     *
     * @return \Crm\Models\Users
     */
    public function getUser()
    {
        $identity = $this->session->get('auth-identity');
        if (isset($identity['id']) && ($identity['id'] !== 'admin')) {

            $user = Users::findFirst(array(
                array('_id' => $identity['id'] )
            ));
            if ($user == false) {
                throw new \Exception('The user does not exist');
            }

            return $user;
        } elseif ($identity['id'] == 'admin') {
            $user = new Users();

            $user->_id = $identity['id'];
            $user->role = 'admin';
            $user->profile = 'admin';
            $user->name = $identity['name'];
            $user->email = $identity['name'];
            return $user;
        } else {
            throw new \Exception('Please login first');
        }

        return false;
    }

    /**
     * Check if current user is admin
     *
     * @return bool
     * @throws \Exception
     */
    public function getIsAdmin()
    {
        $user = $this->getUser();

        if (!$user) return false;

        if (!$this->config->admin) {
            throw new \Exception('Error in config file - admin - section');
        }

        if ($user->email == $this->config->admin->username) {
            return true;
        }

        return false;
    }

    public function getIsLoggedIn()
    {
        $identity = $this->getIdentity();

        return !empty($identity) ? true : false;
    }

    public function getIsUserBelongsToGroup($groupName)
    {
        $user = $this->getUser();

        if (!$user) return false;

        $identity = $this->getIdentity();

        if (!$identity) return false;

        if ($groupName == $identity['profile']) return true;

        return false;
    }

    public function getIsUserGroupAdmin($groupName)
    {
        $user = $this->getUser();

        if (!$user) return false;

        $identity = $this->getIdentity();

        if (!$identity) return false;

        if (($groupName . "_admin") == $identity['profile']) return true;
    }
}