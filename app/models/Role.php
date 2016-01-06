<?php

namespace Crm\Models;

class Role extends CollectionBase
{

    /**
     *
     * @var string
     */
    public $_id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var array()
     */
    public $permissions = [];

}
