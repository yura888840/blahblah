<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 13.03.15
 * Time: 12:23
 */

namespace Crm\Resources;


class ResourceBase extends \Phalcon\Acl\Resource
{

    public $element;

    public $parent;


    /**
     * Resource Control List
     *  List of the resources inside object to control. Allow - Deny
     *
     * @var array
     */
    public $rcl;

    public function __construct($name, $description = NULL, $obj = NULL)
    {
        $this->element = $obj;

        parent::__construct($name, $description);
    }


    public function getParentNode()
    {

    }

    public function getPermissionParentNode()
    {

    }
/*
    public function __call($name, array $params)
    {
        //call_user_func_array(array($this->element, $name), $params);
    }

    public static function __callStatic($name, array $params)
    {
        //forward_static_call_array(array($this->element, $name), $params);
    }*/

} 