<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 16.09.15
 * Time: 19:18
 */

namespace Crm\Models;


class SimplePermissions extends CollectionBase
{

    public $_id;

    public $userId;

    public $permisssions = [
        'google_calendar' => false,
        'ticket_assign' => false,
    ];
}