<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 20.02.15
 * Time: 13:11
 */

namespace Crm\Models;


class Permissions extends CollectionBase
{

    public $_id;

    public $profile;

    public $resource;

    public $action;

    public $type;

    public $name;

    public $permissions;

    public $description;

    public $operation;

}