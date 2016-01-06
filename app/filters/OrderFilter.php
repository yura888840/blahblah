<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 02.03.15
 * Time: 16:01
 */

namespace Crm\Filters;


class OrderFilter {

    private $orders = array(
        'Projects',
        'Date Added',
        'status',
        'priority',
    );

    public function filter($value)
    {
        return in_array($value, $this->orders) ? $value : false;
    }
} 