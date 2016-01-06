<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 26.10.15
 * Time: 18:47
 */

namespace Crm\Models;

trait CollectionWrapperTrait
{
    public function __construct($params)
    {
        if (is_array($params)) {
            foreach ($params as $k => $v) {
                if (property_exists(self, $k))
                    $this->{$k} = $v;
            }
        }

        parent::__construct($params);
    }
}