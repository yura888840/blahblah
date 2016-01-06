<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 24.04.15
 * Time: 15:47
 */

namespace Crm\Models;


class Resources extends CollectionBase
{

    public $_id;

    /**
     * name of the resource
     * @var string
     */
    public $resource;

    /**
     * Type of resource
     * @var string module or something else
     */
    public $type;

    /**
     * Controller of resource
     * @var string
     */
    public $controller;

    /**
     * List of operation on module via appropriate actions
     * @var array
     */
    public $operations;

    public $parent = NULL;

}