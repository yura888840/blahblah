<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19.02.15
 * Time: 12:32
 */

namespace Crm\Models;

//@todo cleanup
/**
 * Class Users
 *  Authorization - by email
 * @package Crm\Models
 */
class Users extends CollectionBase
{

    public $_id;

    public $email;

    public $name;

    public $user;

    public $password;

    public $mustChangePassword;

    public $profilesId;

    public $banned;

    public $suspended;

    public $active;

    public $profile = 'guest';

    public $widgets; //resources

    public $status;

    public $role = 'guest';

    public $deleted;

    public function beforeSave()
    {
        if (empty($this->created)) {
            $this->created = new \MongoDate();
        }
    }

}