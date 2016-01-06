<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 23.02.15
 * Time: 13:25
 */

namespace Crm\Models;


class ResetPassword extends CollectionBase
{

    public $_id;

    public $usersId;

    public $code;

    public $createdAt;

    public $modifiedAt;

    public $reset;


} 