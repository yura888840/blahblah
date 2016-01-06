<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 07.04.15
 * Time: 13:52
 */

namespace Crm\Models;


class Widgets extends CollectionBase
{
    public $_id;

    public $widgetId;

    public $name;

    public $optName;

    public $paramsWidget;

    public $jsList;

    public $css;

    public $template;

    public $permissions;

    public $widgetFactoryType;

    public $size;

}