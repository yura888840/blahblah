<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 02.03.15
 * Time: 13:26
 */

namespace Crm\Filters;


class PriorityFilter {

    private $priorities = array(
        'Low Priority',
        'Normal Priority',
        'Urgent Priority',
        'Urgent Issue',
    );

    public function filter($value)
    {
        return in_array($value, $this->priorities) ? $value : false;
    }
} 