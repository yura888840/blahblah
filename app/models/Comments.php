<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 27.02.15
 * Time: 15:29
 */

namespace Crm\Models;


class Comments extends CollectionBase
{
    public $_id;
    
    public $parent_id;

    public $parent_name;
    
    public $user_id;

    public $user_name;
    
    public $text;

    public $attach;

    public $isPrivate = true;

    public $notified;

    public function beforeSave()
    {
        if(empty($this->created)) {
            $this->created = new \MongoDate();
        }
    }

} 