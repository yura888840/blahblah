<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 20.02.15
 * Time: 12:57
 */

namespace Crm\Models;


class Profiles extends CollectionBase
{

    public $_id;

    public $name;

    public $active;

    public $created;

    public $modified;

    //@todo fix logics
    public function beforeSave()
    {
        if(empty($this->created)) {
            $this->created = new \MongoDate();
        }

        if (empty($this->modified)) {
            $this->modified = new \MongoDate();
        }
    }
} 