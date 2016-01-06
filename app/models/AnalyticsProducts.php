<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 03.03.15
 * Time: 16:14
 */

namespace Crm\Models;


class AnalyticsProducts extends CollectionBase
{

    public $_id;

    public $name;

    public $active;

    private function beforeSave()
    {
        foreach ($this as $key => &$value) {
            if (($key=='updated_at' or $key=='created_at') and is_string($value)){
                $value = new \MongoDate(strtotime($value));
            }
        }
    }

} 