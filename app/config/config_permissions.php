<?php

$configPermissions = new \Phalcon\Config(array(
    'permissions' => array(
        'setDefaultDENY' => false,
        'resources' => array(
            'public' => array(
                'auth' => array('view'),
            ),
            'private' => array(
                'dashboard' => array('view', 'update'),
                'tickets' => array('view', 'update', 'create'),
                'calendars-getAjaxEvents' => array('view'),
            ),
        ),
        'allow' => array(
            array('admin', 'dashboard', 'view'),
            array('admin', 'dashboard', 'update'),
            array('user', 'dashboard', 'view'),
        ),
        'deny' => array(
            array('guest', 'dashboard', 'view'),
            array('guest', 'dashboard', 'update'),
            array('guest', 'calendars-getAjaxEvents', 'view'),
        ),
    ),
));

return $configPermissions;