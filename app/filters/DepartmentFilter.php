<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 02.03.15
 * Time: 13:27
 */

namespace Crm\Filters;


class DepartmentFilter extends \Phalcon\Acl\Resource
{

    public function filter($value)
    {
        return $value;
    }
} 