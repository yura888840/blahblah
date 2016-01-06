<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 21.04.15
 * Time: 15:35
 */

namespace Crm\Models;

/**
 * Class PrivateResources  List of private to ACL resources
 * @package Crm\Models
 */
class PrivateResources extends CollectionBase
{

    public $_id;

    public $name;

    public $operation;

    public $description;

    public $resource;

    public $action;

    public function beforeValidationOnCreate()
    {
        echo "This is executed before creating !";
    }

}