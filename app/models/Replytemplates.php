<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 18.06.15
 * Time: 14:59
 */

namespace Crm\Models;


class Replytemplates extends CollectionBase
{
    public $_id;

    public $name;

    public $body;

    private function beforeSave()
    {

    }

}